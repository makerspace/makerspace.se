<?php

/**
 * @file
 * Contains \Drupal\comment\Controller\CommentController.
 */

namespace Drupal\comment\Controller;

use Drupal\comment\CommentInterface;
use Drupal\comment\CommentManagerInterface;
use Drupal\field\FieldInfo;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Controller for the comment entity.
 *
 * @see \Drupal\comment\Entity\Comment.
 */
class CommentController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The HTTP kernel.
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * Field info service.
   *
   * @var \Drupal\field\FieldInfo
   */
  protected $fieldInfo;

  /**
   * The comment manager service.
   *
   * @var \Drupal\comment\CommentManagerInterface
   */
  protected $commentManager;

  /**
   * Constructs a CommentController object.
   *
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
   *   HTTP kernel to handle requests.
   * @param \Drupal\field\FieldInfo $field_info
   *   Field Info service.
   * @param \Drupal\comment\CommentManagerInterface $comment_manager
   *   The comment manager service.
   */
  public function __construct(HttpKernelInterface $http_kernel, FieldInfo $field_info, CommentManagerInterface $comment_manager) {
    $this->httpKernel = $http_kernel;
    $this->fieldInfo = $field_info;
    $this->commentManager = $comment_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_kernel'),
      $container->get('field.info'),
      $container->get('comment.manager')
    );
  }

  /**
   * Publishes the specified comment.
   *
   * @param \Drupal\comment\CommentInterface $comment
   *   A comment entity.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse.
   *   Redirects to the permalink URL for this comment.
   */
  public function commentApprove(CommentInterface $comment) {
    $comment->status->value = CommentInterface::PUBLISHED;
    $comment->save();

    drupal_set_message($this->t('Comment approved.'));
    $permalink_uri = $comment->permalink();
    $permalink_uri['options']['absolute'] = TRUE;
    $url = $this->urlGenerator()->generateFromPath($permalink_uri['path'], $permalink_uri['options']);
    return new RedirectResponse($url);
  }

  /**
   * Redirects comment links to the correct page depending on comment settings.
   *
   * Since comments are paged there is no way to guarantee which page a comment
   * appears on. Comment paging and threading settings may be changed at any
   * time. With threaded comments, an individual comment may move between pages
   * as comments can be added either before or after it in the overall
   * discussion. Therefore we use a central routing function for comment links,
   * which calculates the page number based on current comment settings and
   * returns the full comment view with the pager set dynamically.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request of the page.
   * @param \Drupal\comment\CommentInterface $comment
   *   A comment entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The comment listing set to the page on which the comment appears.
   */
  public function commentPermalink(Request $request, CommentInterface $comment) {
    if ($entity = $this->entityManager()->getStorageController($comment->entity_type->value)->load($comment->entity_id->value)) {
      // Check access permissions for the entity.
      if (!$entity->access('view')) {
        throw new AccessDeniedHttpException();
      }
      $instance = $this->fieldInfo->getInstance($entity->entityType(), $entity->bundle(), $comment->field_name->value);

      // Find the current display page for this comment.
      $page = comment_get_display_page($comment->id(), $instance);
      // @todo: Cleaner sub request handling.
      $uri = $entity->uri();
      $redirect_request = Request::create($uri['path'], 'GET', $request->query->all(), $request->cookies->all(), array(), $request->server->all());
      $redirect_request->query->set('page', $page);
      // @todo: Convert the pager to use the request object.
      $request->query->set('page', $page);
      return $this->httpKernel->handle($redirect_request, HttpKernelInterface::SUB_REQUEST);
    }
    throw new NotFoundHttpException();
  }

  /**
   * Redirects legacy node links to the new path.
   *
   * @param \Drupal\Core\Entity\EntityInterface $node
   *   The node object identified by the legacy URL.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirects user to new url.
   */
  public function redirectNode(EntityInterface $node) {
    $fields = $this->commentManager->getFields('node');
    // Legacy nodes only had a single comment field, so use the first comment
    // field on the entity.
    if (!empty($fields) && ($field_names = array_keys($fields)) && ($field_name = reset($field_names))) {
      return new RedirectResponse($this->urlGenerator()->generateFromPath('comment/reply/node/' . $node->id() . '/' . $field_name, array('absolute' => TRUE)));
    }
    throw new NotFoundHttpException();
  }

  /**
   * Form constructor for the comment reply form.
   *
   * There are several cases that have to be handled, including:
   *   - replies to comments
   *   - replies to entities
   *   - attempts to reply to entities that can no longer accept comments
   *   - respecting access permissions ('access comments', 'post comments',
   *     etc.)
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   * @param string $entity_type
   *   Every comment belongs to an entity. This is the type of the entity.
   * @param string $entity_id
   *   Every comment belongs to an entity. This is the ID of the entity.
   * @param string $field_name
   *   The field_name to which the comment belongs.
   * @param int $pid
   *   (optional) Some comments are replies to other comments. In those cases,
   *   $pid is the parent comment's comment ID. Defaults to NULL.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
   *   One of the following:
   *   An associative array containing:
   *   - An array for rendering the entity or parent comment.
   *     - comment_entity: If the comment is a reply to the entity.
   *     - comment_parent: If the comment is a reply to another comment.
   *   - comment_form: The comment form as a renderable array.
   *   - An associative array containing:
   *     - An array for rendering the entity or parent comment.
   *        - comment_entity: If the comment is a reply to the entity.
   *        - comment_parent: If the comment is a reply to another comment.
   *     - comment_form: The comment form as a renderable array.
   *   - A redirect response to current node:
   *     - If user is not authorized to post comments.
   *     - If parent comment doesn't belong to current entity.
   *     - If user is not authorized to view comments.
   *     - If current entity comments are disable.
   */
  public function getReplyForm(Request $request, $entity_type, $entity_id, $field_name, $pid = NULL) {

    // Check if entity and field exists.
    $fields = $this->commentManager->getFields($entity_type);
    if (empty($fields[$field_name]) || !($entity = $this->entityManager()->getStorageController($entity_type)->load($entity_id))) {
      throw new NotFoundHttpException();
    }

    $account = $this->currentUser();
    $uri = $entity->uri();
    $build = array();

    // Check if the user has the proper permissions.
    if (!$account->hasPermission('post comments')) {
      drupal_set_message($this->t('You are not authorized to post comments.'), 'error');
      return new RedirectResponse($this->urlGenerator()->generateFromPath($uri['path'], array('absolute' => TRUE)));
    }

    // The user is not just previewing a comment.
    if ($request->request->get('op') != $this->t('Preview')) {
      $status = $entity->{$field_name}->status;
      if ($status != COMMENT_OPEN) {
        drupal_set_message($this->t("This discussion is closed: you can't post new comments."), 'error');
        return new RedirectResponse($this->urlGenerator()->generateFromPath($uri['path'], array('absolute' => TRUE)));
      }

      // $pid indicates that this is a reply to a comment.
      if ($pid) {
        // Check if the user has the proper permissions.
        if (!$account->hasPermission('access comments')) {
          drupal_set_message($this->t('You are not authorized to view comments.'), 'error');
          return new RedirectResponse($this->urlGenerator()->generateFromPath($uri['path'], array('absolute' => TRUE)));
        }
        // Load the parent comment.
        $comment = $this->entityManager()->getStorageController('comment')->load($pid);
        // Check if the parent comment is published and belongs to the entity.
        if (($comment->status->value == CommentInterface::NOT_PUBLISHED) || ($comment->entity_id->value != $entity->id())) {
          drupal_set_message($this->t('The comment you are replying to does not exist.'), 'error');
          return new RedirectResponse($this->urlGenerator()->generateFromPath($uri['path'], array('absolute' => TRUE)));
        }
        // Display the parent comment.
        $build['comment_parent'] = $this->entityManager()->getViewBuilder('comment')->view($comment);
      }

      // The comment is in response to a entity.
      elseif ($entity->access('view', $account)) {
        // We make sure the field value isn't set so we don't end up with a
        // redirect loop.
        $entity->{$field_name}->status = COMMENT_HIDDEN;
        // Render array of the entity full view mode.
        $build['commented_entity'] = $this->entityManager()->getViewBuilder($entity->entityType())->view($entity, 'full');
        unset($build['commented_entity']['#cache']);
        $entity->{$field_name}->status = $status;
      }
    }
    else {
      $build['#title'] = $this->t('Preview comment');
    }

    // Show the actual reply box.
    $comment = $this->entityManager()->getStorageController('comment')->create(array(
      'entity_id' => $entity->id(),
      'pid' => $pid,
      'entity_type' => $entity->entityType(),
      'field_id' => $entity->entityType() . '__' . $field_name,
    ));
    $build['comment_form'] = $this->entityManager()->getForm($comment);

    return $build;
  }

  /**
   * Returns a set of nodes' last read timestamps.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request of the page.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function renderNewCommentsNodeLinks(Request $request) {
    if ($this->currentUser()->isAnonymous()) {
      throw new AccessDeniedHttpException();
    }

    $nids = $request->request->get('node_ids');
    $field_name = $request->request->get('field_name');
    if (!isset($nids)) {
      throw new NotFoundHttpException();
    }
    // Only handle up to 100 nodes.
    $nids = array_slice($nids, 0, 100);

    $links = array();
    foreach ($nids as $nid) {
      $node = node_load($nid);
      $new = comment_num_new($node->id(), 'node');
      $query = comment_new_page_count($node->{$field_name}->comment_count, $new, $node);
      $links[$nid] = array(
        'new_comment_count' => (int) $new,
        'first_new_comment_link' => $this->urlGenerator()->generateFromPath('node/' . $node->id(), array('query' => $query, 'fragment' => 'new')),
      );
    }

    return new JsonResponse($links);
  }

}

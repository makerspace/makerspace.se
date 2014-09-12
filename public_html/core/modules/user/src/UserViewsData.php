<?php

/**
 * @file
 * Contains \Drupal\user\UserViewsData.
 */

namespace Drupal\user;

use Drupal\views\EntityViewsDataInterface;

/**
 * Provides the views data for the user entity type.
 */
class UserViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    // Define the base group of this table. Fields that don't have a group defined
    // will go into this field by default.
    $data['users']['table']['group']  = t('User');

    $data['users']['table']['base'] = array(
      'field' => 'uid',
      'title' => t('User'),
      'help' => t('Users who have created accounts on your site.'),
      'access query tag' => 'user_access',
    );
    $data['users']['table']['entity type'] = 'user';
    $data['users']['table']['wizard_id'] = 'user';

    $data['users_field_data']['table']['group'] = t('User');
    $data['users_field_data']['table']['entity type'] = 'user';
    $data['users_field_data']['table']['join']['users'] = array(
      'type' => 'INNER',
      'left_field' => 'uid',
      'field' => 'uid',
    );

    $data['users']['uid'] = array(
      'title' => t('Uid'),
      'help' => t('The user ID'),
      'field' => array(
        'id' => 'user',
      ),
      'argument' => array(
        'id' => 'user_uid',
        'name table' => 'users_field_data',
        'name field' => 'name',
        'empty field name' => \Drupal::config('user.settings')->get('anonymous'),
      ),
      'filter' => array(
        'title' => t('Name'),
        'id' => 'user_name',
      ),
      'sort' => array(
        'id' => 'standard',
      ),
      'relationship' => array(
        'title' => t('Content authored'),
        'help' => t('Relate content to the user who created it. This relationship will create one record for each content item created by the user.'),
        'id' => 'standard',
        'base' => 'node_field_data',
        'base field' => 'uid',
        'field' => 'uid',
        'label' => t('nodes'),
      ),
    );

    $data['users']['uid_raw'] = array(
      'help' => t('The raw numeric user ID.'),
      'real field' => 'uid',
      'filter' => array(
        'title' => t('The user ID'),
        'id' => 'numeric',
      ),
    );

    $data['users']['uid_representative'] = array(
      'relationship' => array(
        'title' => t('Representative node'),
        'label'  => t('Representative node'),
        'help' => t('Obtains a single representative node for each user, according to a chosen sort criterion.'),
        'id' => 'groupwise_max',
        'relationship field' => 'uid',
        'outer field' => 'users.uid',
        'argument table' => 'users',
        'argument field' => 'uid',
        'base' => 'node',
        'field' => 'nid',
        'relationship' => 'node_field_data:uid'
      ),
    );

    $data['users']['uid_current'] = array(
      'real field' => 'uid',
      'title' => t('Current'),
      'help' => t('Filter the view to the currently logged in user.'),
      'filter' => array(
        'id' => 'user_current',
        'type' => 'yes-no',
      ),
    );

    $data['users_field_data']['name'] = array(
      'title' => t('Name'),
      'help' => t('The user or author name.'),
      'field' => array(
        'id' => 'user_name',
      ),
      'sort' => array(
        'id' => 'standard',
      ),
      'argument' => array(
        'id' => 'string',
      ),
      'filter' => array(
        'id' => 'string',
        'title' => t('Name (raw)'),
        'help' => t('The user or author name. This filter does not check if the user exists and allows partial matching. Does not utilize autocomplete.')
      ),
    );

    // Note that this field implements field level access control.
    $data['users_field_data']['mail'] = array(
      'title' => t('Email'),
      'help' => t('Email address for a given user. This field is normally not shown to users, so be cautious when using it.'),
      'field' => array(
        'id' => 'user_mail',
      ),
      'sort' => array(
        'id' => 'standard',
      ),
      'filter' => array(
        'id' => 'string',
      ),
      'argument' => array(
        'id' => 'string',
      ),
    );

    $data['users']['langcode'] = array(
      'title' => t('Language'),
      'help' => t('Language of the user'),
      'field' => array(
        'id' => 'user_language',
      ),
      'sort' => array(
        'id' => 'standard',
      ),
      'filter' => array(
        'id' => 'language',
      ),
      'argument' => array(
        'id' => 'language',
      ),
    );

    $data['users']['view_user'] = array(
      'field' => array(
        'title' => t('Link to user'),
        'help' => t('Provide a simple link to the user.'),
        'id' => 'user_link',
        'click sortable' => FALSE,
      ),
    );

    $data['users_field_data']['created'] = array(
      'title' => t('Created date'),
      'help' => t('The date the user was created.'),
      'field' => array(
        'id' => 'date',
      ),
      'sort' => array(
        'id' => 'date'
      ),
      'filter' => array(
        'id' => 'date',
      ),
    );

    $data['users_field_data']['created_fulldate'] = array(
      'title' => t('Created date'),
      'help' => t('Date in the form of CCYYMMDD.'),
      'argument' => array(
        'field' => 'created',
        'id' => 'date_fulldate',
      ),
    );

    $data['users_field_data']['created_year_month'] = array(
      'title' => t('Created year + month'),
      'help' => t('Date in the form of YYYYMM.'),
      'argument' => array(
        'field' => 'created',
        'id' => 'date_year_month',
      ),
    );

    $data['users_field_data']['created_year'] = array(
      'title' => t('Created year'),
      'help' => t('Date in the form of YYYY.'),
      'argument' => array(
        'field' => 'created',
        'id' => 'date_year',
      ),
    );

    $data['users_field_data']['created_month'] = array(
      'title' => t('Created month'),
      'help' => t('Date in the form of MM (01 - 12).'),
      'argument' => array(
        'field' => 'created',
        'id' => 'date_month',
      ),
    );

    $data['users_field_data']['created_day'] = array(
      'title' => t('Created day'),
      'help' => t('Date in the form of DD (01 - 31).'),
      'argument' => array(
        'field' => 'created',
        'id' => 'date_day',
      ),
    );

    $data['users_field_data']['created_week'] = array(
      'title' => t('Created week'),
      'help' => t('Date in the form of WW (01 - 53).'),
      'argument' => array(
        'field' => 'created',
        'id' => 'date_week',
      ),
    );

    $data['users_field_data']['access'] = array(
      'title' => t('Last access'),
      'help' => t("The user's last access date."),
      'field' => array(
        'id' => 'date',
      ),
      'sort' => array(
        'id' => 'date'
      ),
      'filter' => array(
        'id' => 'date',
      ),
    );

    $data['users_field_data']['login'] = array(
      'title' => t('Last login'),
      'help' => t("The user's last login date."),
      'field' => array(
        'id' => 'date',
      ),
      'sort' => array(
        'id' => 'date'
      ),
      'filter' => array(
        'id' => 'date',
      ),
    );

    $data['users_field_data']['status'] = array(
      'title' => t('Status'),
      'help' => t('Whether a user is active or blocked.'),
      'field' => array(
        'id' => 'boolean',
        'output formats' => array(
          'active-blocked' => array(t('Active'), t('Blocked')),
        ),
      ),
      'filter' => array(
        'id' => 'boolean',
        'label' => t('Active'),
        'type' => 'yes-no',
      ),
      'sort' => array(
        'id' => 'standard',
      ),
    );

    $data['users_field_data']['changed'] = array(
      'title' => t('Updated date'),
      'help' => t('The date the user was last updated.'),
      'field' => array(
        'id' => 'date',
      ),
      'sort' => array(
        'id' => 'date'
      ),
      'filter' => array(
        'id' => 'date',
      ),
    );

    $data['users_field_data']['changed_fulldate'] = array(
      'title' => t('Updated date'),
      'help' => t('Date in the form of CCYYMMDD.'),
      'argument' => array(
        'field' => 'changed',
        'id' => 'date_fulldate',
      ),
    );

    $data['users_field_data']['changed_year_month'] = array(
      'title' => t('Updated year + month'),
      'help' => t('Date in the form of YYYYMM.'),
      'argument' => array(
        'field' => 'changed',
        'id' => 'date_year_month',
      ),
    );

    $data['users_field_data']['changed_year'] = array(
      'title' => t('Updated year'),
      'help' => t('Date in the form of YYYY.'),
      'argument' => array(
        'field' => 'changed',
        'id' => 'date_year',
      ),
    );

    $data['users_field_data']['changed_month'] = array(
      'title' => t('Updated month'),
      'help' => t('Date in the form of MM (01 - 12).'),
      'argument' => array(
        'field' => 'changed',
        'id' => 'date_month',
      ),
    );

    $data['users_field_data']['changed_day'] = array(
      'title' => t('Updated day'),
      'help' => t('Date in the form of DD (01 - 31).'),
      'argument' => array(
        'field' => 'changed',
        'id' => 'date_day',
      ),
    );

    $data['users_field_data']['changed_week'] = array(
      'title' => t('Updated week'),
      'help' => t('Date in the form of WW (01 - 53).'),
      'argument' => array(
        'field' => 'changed',
        'id' => 'date_week',
      ),
    );

    if (\Drupal::moduleHandler()->moduleExists('filter')) {
      $data['users_field_data']['signature'] = array(
        'title' => t('Signature'),
        'help' => t("The user's signature."),
        'field' => array(
          'id' => 'markup',
          'format' => filter_fallback_format(),
          'click sortable' => FALSE,
        ),
        'filter' => array(
          'id' => 'string',
        ),
      );
    }

    if (\Drupal::moduleHandler()->moduleExists('content_translation')) {
      $data['users']['translation_link'] = array(
        'title' => t('Translation link'),
        'help' => t('Provide a link to the translations overview for users.'),
        'field' => array(
          'id' => 'content_translation_link',
        ),
      );
    }

    $data['users']['edit_node'] = array(
      'field' => array(
        'title' => t('Link to edit user'),
        'help' => t('Provide a simple link to edit the user.'),
        'id' => 'user_link_edit',
        'click sortable' => FALSE,
      ),
    );

    $data['users']['cancel_node'] = array(
      'field' => array(
        'title' => t('Link to cancel user'),
        'help' => t('Provide a simple link to cancel the user.'),
        'id' => 'user_link_cancel',
        'click sortable' => FALSE,
      ),
    );

    $data['users']['data'] = array(
      'title' => t('Data'),
      'help' => t('Provides access to the user data service.'),
      'real field' => 'uid',
      'field' => array(
        'id' => 'user_data',
      ),
    );

    // Define the base group of this table. Fields that don't have a group defined
    // will go into this field by default.
    $data['users_roles']['table']['group']  = t('User');

    // Explain how this table joins to others.
    $data['users_roles']['table']['join'] = array(
      'users' => array(
        'left_field' => 'uid',
        'field' => 'uid',
      ),
    );

    $data['users_roles']['rid'] = array(
      'title' => t('Roles'),
      'help' => t('Roles that a user belongs to.'),
      'field' => array(
        'id' => 'user_roles',
        'no group by' => TRUE,
      ),
      'filter' => array(
        'id' => 'user_roles',
        'allow empty' => TRUE,
      ),
      'argument' => array(
        'id' => 'users_roles_rid',
        'name table' => 'role',
        'name field' => 'name',
        'empty field name' => t('No role'),
        'zero is null' => TRUE,
        'numeric' => TRUE,
      ),
    );

    $data['users_roles']['permission'] = array(
      'title' => t('Permission'),
      'help' => t('The user permissions.'),
      'field' => array(
        'id' => 'user_permissions',
        'no group by' => TRUE,
      ),
      'filter' => array(
        'id' => 'user_permissions',
        'real field' => 'rid',
      ),
    );

    return $data;
  }

}

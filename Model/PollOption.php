<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

App::uses('ForumAppModel', 'Forum.Model');

/**
 * @property Poll $Poll
 * @property PollVote $PollVote
 */
class PollOption extends ForumAppModel {

    /**
     * Display field.
     *
     * @type string
     */
    public $displayField = 'option';

    /**
     * Belongs to.
     *
     * @type array
     */
    public $belongsTo = array(
        'Poll' => array(
            'className' => 'Forum.Poll'
        )
    );

    /**
     * Has many.
     *
     * @type array
     */
    public $hasMany = array(
        'PollVote' => array(
            'className' => 'Forum.PollVote',
            'limit' => 100
        )
    );

    /**
     * Behaviors.
     *
     * @type array
     */
    public $actsAs = array(
        'Utility.Filterable' => array(
            'option' => array(
                'html' => true,
                'strip' => true
            )
        )
    );

    /**
     * Validation.
     *
     * @type array
     */
    public $validations = array(
        'default' => array(
            'poll_id' => array(
                'rule' => 'notEmpty'
            ),
            'option' => array(
                'rule' => 'notEmpty'
            )
        )
    );

    /**
     * Admin settings.
     *
     * @type array
     */
    public $admin = array(
        'iconClass' => 'icon-list',
        'paginate' => array(
            'order' => array('PollOption.topic_id' => 'DESC', 'PollOption.id' => 'ASC')
        )
    );

}

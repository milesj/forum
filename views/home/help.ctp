
<h2><?php __d('forum', 'Help Desk'); ?></h2>

<p><strong><?php __d('forum', 'Why do I need to sign up?'); ?></strong><br />
<?php __d('forum', 'You may read all public forums as a guest, but to be able to post or reply to a topic, you must have a user account.'); ?></p>
                
<p><strong><?php __d('forum', 'Why can\'t I post a topic, poll or reply?'); ?></strong><br />
<?php __d('forum', 'To begin you must have a user account and must be logged in. If you have done this, the topic might be locked or you do not have the proper acces to post in that topic or forum.'); ?></p>

<p><strong><?php __d('forum', 'Why can\'t I login?'); ?></strong><br />
<?php __d('forum', 'The server might be having technicaly difficulties so your request could not be processed. It may also be that your account has been deleted or banned.'); ?></p>

<p><strong><?php __d('forum', 'Oh no, I forgot my password!'); ?></strong><br />
<?php printf(__d('forum', 'If you have forgotten your password, you can reset it using the %s form.', true), $html->link('forgotten password', array('controller' => 'users', 'action' => 'forgot'))); ?></p>

<p><strong><?php __d('forum', 'What does reporting do?'); ?></strong><br />
<?php __d('forum', 'If you find a piece of content that you find inappropriate or offensive, you can report the content for a moderator to delete or fix.'); ?></p>

<p><strong><?php __d('forum', 'What are moderators, super moderators and administrators?'); ?></strong><br />
<?php __d('forum', 'Those are users with higher access and privileges than a regular user. Moderators control and moderate specific forums, where as Super Moderators have full access to all forums. Administrators have full access to all forums as well as editing all the sites settings and configuration. Additionally, moderates have the power to edit and delete other users content.'); ?></p>

<p><strong><?php __d('forum', 'What does the power search do?'); ?></strong><br />
<?php __d('forum', 'By default the search will only search for terms within the topic title. If you check the power search, it will search within the title and post.'); ?></p>

<p><strong><?php __d('forum', 'How do I edit my profile?'); ?></strong><br />
<?php printf(__d('forum', 'Once you have logged in, you can edit your profile by going to the %s link, located at the top right.', true), $html->link('edit profile', array('controller' => 'users', 'action' => 'edit'))); ?></p>

<p><strong><?php __d('forum', 'How do I post a topic?'); ?></strong><br />
<?php __d('forum', 'You would first navigate to the appropriate forum you want to post in. Once there, you would click the "Create Topic" link located at the top and bottom right of the page.'); ?></p>

<p><strong><?php __d('forum', 'How do I post a reply?'); ?></strong><br />
<?php __d('forum', 'When you are reading a topic and are logged in, you would click the "Post Reply" link also located at the top and bottom right of the page.'); ?></p>

<p><strong><?php __d('forum', 'How do I create a poll?'); ?></strong><br />
<?php __d('forum', 'You would create a poll the same way you would create a topic. First enter the correct forum, then hit the "Create Poll" link.'); ?></p>

<p><strong><?php __d('forum', 'How do I edit my topic, poll, post, etc?'); ?></strong><br />
<?php __d('forum', 'When you are reading a topic, at the top right of each post you will see a few text links. Hit the "Edit" link to edit your respective content. If you are editing the first post of a topic, it will additionally edit the topic or poll as well. You may only edit your own posts or topics unless you have moderating capabilities.'); ?></p>

<p><strong><?php __d('forum', 'How do I report a topic, post or user?'); ?></strong><br />
<?php __d('forum', 'At the top right of each post you would click the "Report <content>" link. From there you should add a comment on why you are reporting this content.'); ?></p>

<p><strong><?php __d('forum', 'How do I get higher access and permissions?'); ?></strong><br />
<?php __d('forum', 'It is up to the administrator to give you higher access. All you can do is be a superb member on the forum and hope they promote you.'); ?></p>

<p><strong><?php __d('forum', 'I have more questions that aren\'t shown here!'); ?></strong><br />
<?php printf(__d('forum', 'If you have additional questions and need further help, please contact us at %s.', true), $cupcake->settings['site_email']); ?></p>
Hi,

You asked to be notified on topic with name "<?php echo $post['Topic']['title'];?>".  The following was posted on the topic by user "<?php echo $post['User']['username'];?>".

<?php echo $post['Post']['content'];?>


To read and reply and unsubscribe from this topic please visit: http://<?php echo env("HTTP_HOST");?>/forum/topics/view/<?php echo $post['Topic']['slug'];?>#post-<?php echo $post['Post']['id'];?>


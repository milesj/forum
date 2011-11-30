Hi,

You asked to be notified on new topics in the forum with name "<?php echo $topic['Forum']['title'];?>".  The following was posted on the topic by user "<?php echo $topic['User']['username'];?>".

<?php echo $topic['Topic']['title'];?>

<?php echo $topic['FirstPost']['content'];?>


To read and reply and unsubscribe from this topic please visit: http://<?php echo env("HTTP_HOST");?>/forum/topics/view/<?php echo $topic['Topic']['slug'];?>#post-<?php echo $topic['FirstPost']['id'];?>


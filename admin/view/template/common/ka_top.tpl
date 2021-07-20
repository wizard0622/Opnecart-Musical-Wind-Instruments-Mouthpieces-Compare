<?php
/*
  Project: Ka Extensions
  Author : karapuz <support@ka-station.com>

  Version: 2 ($Revision: 34 $)
*/

?>
<?php if (!empty($breadcrumbs)) { $breadcrumb_started = false; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <?php if (!empty($breadcrumb_started)) { echo ' :: '; } $breadcrumb_started = true;  ?>
      <?php if (!empty($breadcrumb['href'])) { ?>
        <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
      <?php } else { ?>
        <?php echo $breadcrumb['text']; ?>
      <?php } ?>
    <?php } ?>
  </div>
<?php } ?>

<?php if (!empty($top_messages)) { ?>
  <?php foreach ($top_messages as $top_message) { ?>
    <?php if ($top_message['type'] == 'E') { ?>
    <div class="warning"><?php echo $top_message['content']; ?></div>
    <?php } else { ?>
    <div class="success"><?php echo $top_message['content']; ?></div>
    <?php } ?>
  <?php } ?>
<?php } ?>
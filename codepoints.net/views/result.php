<?php
$query = $result->getQuery();
$cQuery = count($query);
$cBlocks = count($blocks);
$cBResult = $cBlocks > 0? ($cBlocks > 1?
    sprintf(__(' and %s Blocks'), $cBlocks) :
    __(' and 1 Block')) :
    '';
$fQuery = $result->getCount();
$title = $fQuery > 0? ($fQuery > 1?
    sprintf(__('%s Codepoints%s Found'), $fQuery, $cBResult) :
        sprintf(__('1 Codepoint%s Found'), $cBResult)) :
        ((isset($cps) && count($cps))?
            sprintf(__('The Following Codepoints%s Match'), $cBResult) :
                sprintf(__('No Codepoints%s Found'), $cBResult));
if ($fQuery === 0) {
    $hDescription = __("No codepoints match the given search.");
} else {
    $hDescription = sprintf(__('%s codepoints match the given search for %s properties.'), $fQuery, $cQuery);
    if ($page && $page > 1) {
        $hDescription .= ' ' . sprintf(__('This is page %s of %s.'), $page, $pagination->getNumberOfPages());
    } else {
        $page = 1;
    }
}
include "header.php";
include "nav.php";
?>
<div class="payload search">
  <h1><?php e($title)?></h1>
  <?php if (isset($wizard) && $wizard):?>
    <p>
      <a href="<?php e($router->getUrl('wizard'))?>"><?php _e('Try “Find My Codepoint” again.')?></a>
    </p>
  <?php endif?>
  <?php if ($fQuery > 0):?>
    <?php /* <p><strong><?php e($fQuery)? ></strong> codepoints match<?php include "result/querytext.php"? ></p>*/?>
    <div class="cp-list" data-page="<?php echo $page?>">
      <?php echo $pagination?>
      <ol class="block data">
        <?php foreach ($result->get() as $cp => $na):
          echo '<li value="' . $cp . '">'; cp($na); echo '</li>';
        endforeach ?>
      </ol>
      <?php echo $pagination?>
    </div>
  <?php else:?>
    <?php if (isset($cps) && count($cps)):?>
      <ul class="data">
        <?php foreach($cps as $cp):?>
          <li><?php cp($cp)?></li>
        <?php endforeach?>
      </ul>
    <?php endif?>
  <?php endif?>
  <?php if($cBlocks):?>
  <p><?php printf($cBlocks === 1? __('%s block matches %s:') : __('%s blocks match %s:'), '<strong>'.q($cBlocks).'</strong>', '<strong>'.q(_get('q')).'</strong>')?><p>
    <ol class="data">
      <?php foreach ($blocks as $bl):
        echo '<li>'; bl($bl); echo '</li>';
      endforeach ?>
    </ol>
  <?php endif?>
  <?php include "search/form.php"?>
</div>
<?php
$footer_scripts = array('/static/js/searchform.js');
include "footer.php"?>

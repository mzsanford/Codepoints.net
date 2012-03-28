<?php
$query = $result->getQuery();
$cQuery = count($query);
$cBlocks = count($blocks);
$cBResult = $cBlocks > 0? ($cBlocks > 1?
    sprintf(' and %s Blocks', $cBlocks) :
    ' and 1 Block') :
    '';
$fQuery = $result->getCount();
$title = $cQuery > 0? ($cQuery > 1?
    sprintf('%s Codepoints%s Found', $fQuery, $cBResult) :
    sprintf('1 Codepoint%s Found', $cBResult)) : 'Search';
include "header.php";
include "nav.php";
?>
<div class="payload search">
  <h1><?php e($title)?></h1>
  <?php if ($fQuery > 0):?>
  <p>There are <strong><?php e($fQuery)?></strong> codepoints<?php if ($cQuery > 0):?>
    where
      <?php foreach ($query as $i => $q):
        $sep = ', ';
        if (in_array($cQuery, array(1, $i+1))) {
            $sep = '.';
        } elseif ($i === $cQuery - 2) {
            $sep = $q[3] === 'AND'? ' and ' : ' or ';
        }
        $tmp = array();
        foreach ((array)$q[2] as $q2) {
            $tmp[] = ($q2? $info->getLabel($q[0], trim($q2, '%')) : 'empty');
        }
        switch ($q[1]) {
            case 'LIKE':
            case 'like':
                $ct = 'contains';
                break;
            case '!=':
                $ct = 'is not';
                break;
            default:
                $ct = 'is';
        }
        printf('<span class="where"><em>%s</em> %s <strong>%s</strong></span>%s',
            $info->getCategory($q[0]),
            $ct,
            join('</strong> or <strong>', $tmp), $sep);
      endforeach ?>
    <?php elseif (isset($range)):?>
      in the range <strong><?php e($range)?></strong>.
    <?php else:?>.
    <?php endif?>
  </p>
  <?php echo $pagination?>
  <ol class="block data">
    <?php foreach ($result->get() as $cp => $na):
      echo '<li value="' . $cp . '">'; cp($na); echo '</li>';
    endforeach ?>
  </ol>
  <?php echo $pagination?>
  <?php else:?>
    <p>Please enter your search specification.</p>
  <?php endif?>
  <?php if($cBlocks):?>
  <p><strong><?php e($cBlocks)?></strong> block<?php if($cBlocks > 1):?>s<?php endif?>
    match<?php if($cBlocks === 1):?>es<?php endif?>
    <strong><?php e(_get('q'))?></strong>:<p>
    <ol class="data">
      <?php foreach ($blocks as $bl):
        echo '<li>'; bl($bl); echo '</li>';
      endforeach ?>
    </ol>
  <?php endif?>
  <?php include "searchform.php"?>
</div>
<?php include "footer.php"?>

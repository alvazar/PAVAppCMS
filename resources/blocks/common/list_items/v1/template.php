<?php
$items = $data['items'] ?? [];
?>
<?php
if (!empty($data['title'])) {
    ?>
    <div class="title"><?= $data['title'] ?></div>
    <?php
}
?>
<div class="ListItems">
<?php
if (!empty($items)) {
    foreach ($items as $item) {
        ?>
        <div class="item">
            <div class="title"><?= $item['title'] ?? '' ?></div>
            <div class="text"><?= nl2br($item['text'] ?? '') ?></div>
        </div>
        <?php
    }
}
?>
</div>

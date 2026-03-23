<?php
$cpt = 'product'; 
$filters = get_field('filter_items','option'); 
$min_price = 0; $max_price = 500; 
?>

<div id="dynamic-filters">
    <div id="filters">
        <?php foreach($filters as $filter):
            $label = $filter['label'];
            $type = $filter['filter_type'];
            $input = $filter['input_type'];
            $field = $filter['field_name'];
            $options = $filter['options'];
        ?>
            <div class="filter-group" data-filter-key="<?= esc_attr($field) ?>">
                <label><?= esc_html($label) ?></label>

                <?php if($input==='select' || $input==='checkbox'):
                    foreach(explode(',',$options) as $opt): ?>
                        <label>
                            <input type="<?= $input ?>" name="<?= esc_attr($field) ?>" value="<?= esc_attr(trim($opt)) ?>">
                            <?= esc_html(trim($opt)) ?>
                        </label>
                    <?php endforeach;
                elseif($input==='range'): ?>
                    <div class="range-slider" data-field="<?= esc_attr($field) ?>"></div>
                    <div>£<span class="min-val"><?= $min_price ?></span> - £<span class="max-val"><?= $max_price ?></span></div>
                <?php else: ?>
                    <input type="text" name="<?= esc_attr($field) ?>">
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>

    <div id="results"></div>
    <button id="load_more" data-page="1">Load More</button>
</div>
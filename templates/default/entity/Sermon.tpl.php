<article class="h-review known-sermon">
    <?php
        if (\Idno\Core\site()->template()->getTemplateType() == 'default') {
            ?>
            <h2 class="p-name"><span class="fa fa-globe-e"></span> 
                <a class="u-url" href="<?= $vars['object']->getDisplayURL() ?>">Sermon Notes for "<i><?= htmlentities(strip_tags($vars['object']->getTitle()), ENT_QUOTES, 'UTF-8'); ?></i>" </a>
			<span class="h-geo">
				<data class="p-latitude" value="<?= $object->lat ?>"></data>
				<data class="p-longitude" value="<?= $object->long ?>"></data>
			</span>
            </h2>
            <?php
        }
    ?>

<?php if (!empty($vars['object']->getSeries())) {?>
			<div class=""><p><span class="fa fa-list-alt"></span> Sermon Series: <?= htmlentities(strip_tags($vars['object']->getSeries()), ENT_QUOTES, 'UTF-8'); ?></span></p></div>
<?php } ?>

<?php if (!empty($vars['object']->getSpeaker())) {?>
			<div class=""><p><span class="fa fa-male"></span> Speaker: <?= htmlentities(strip_tags($vars['object']->getSpeaker()), ENT_QUOTES, 'UTF-8'); ?></span></p></div>
<?php } ?>

<?php if (!empty($vars['object']->getSermonURL())) {?>
			<div class=""><p><span class="fas fa-play-circle-o"> </span> Listen to "<i><a href="<?= $vars['object']->getSermonURL() ?>" class="p-name"><?= htmlentities(strip_tags($vars['object']->getTitle()), ENT_QUOTES, 'UTF-8'); ?></a></span></p></div>
<?php } ?>

<?php if (!empty($vars['object']->getScripture())) {?>
			<div class=""><p><span class="fa fa-bookmark-o"></span> Key Scripture: <?= htmlentities(strip_tags($vars['object']->getScripture()), ENT_QUOTES, 'UTF-8'); ?></span></p></div>
<?php } ?>

<?php if (!empty($vars['object']->getSermonDate())) {?>
			<div class=""><p><span class="fa fa-calendar"></span> Sermon Date and Time: <?= htmlentities(strip_tags($vars['object']->getSermonDate()), ENT_QUOTES, 'UTF-8'); ?></span></p></div>
<?php } ?>

<?php if (!empty($vars['object']->getPlaceName())) {?>
			<div class=""><p><span class="fa fa-map-marker"></span> Location: <a href="<?= $vars['object']->getChurchURL() ?>" class="p-name"><?= htmlentities(strip_tags($vars['object']->getPlaceName()), ENT_QUOTES, 'UTF-8'); ?></a></span></p></div>
<?php } ?>

<?php if (!empty($vars['object']->getLocation())) {?>
			<div class=""><p><span class="fa fa-location-arrow"></span> Address: <?= htmlentities(strip_tags($vars['object']->getLocation()), ENT_QUOTES, 'UTF-8'); ?></span></p></div>
<?php } ?>

<?php

    if (empty($vars['feed_view'])) {


	if (!empty($object->lat)) {

	if (\Idno\Core\Idno::site()->currentPage()->isPermalink())
	{
	?>

        <div id="map_<?= $object->_id ?>" style="height: 200px;"></div>
<?php }
	} ?>

	<?php
    }
    ?>
    <div class="p-map">
	    <?php
        if ($attachments = $vars['object']->getAttachments()) {
            foreach ($attachments as $attachment) {
                $mainsrc = $attachment['url'];
                if (!empty($vars['object']->thumbnail_large)) {
                    $src = $vars['object']->thumbnail_large;
                } else if (!empty($vars['object']->thumbnail)) { // Backwards compatibility
                    $src = $vars['object']->thumbnail;
                } else {
                    $src = $mainsrc;
                }
                
                // Patch to correct certain broken URLs caused by https://github.com/idno/known/issues/526
                $src = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\site()->config()->getDisplayURL(), $src);
                $mainsrc = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\site()->config()->getDisplayURL(), $mainsrc);

                ?>
                <p style="text-align: center">
                    <a href="<?= $this->makeDisplayURL($mainsrc) ?>"><img src="<?= $this->makeDisplayURL($src) ?>" class="u-photo"/></a>
                </p>
            </div>
            <?php
            }
        }
    ?>
	
        <?php
            if (!empty($object->body)) {
                echo $this->autop($this->parseURLs($this->parseHashtags($object->body)));
            }

            if (!empty($object->tags)) {
                ?>

                <p class="tag-row"><i class="icon-tag"></i> <?= $this->parseHashtags($object->tags) ?></p>

            <?php } ?>
    </div>
			
            <div class="p-item h-product">
                                

<?php

    if (empty($vars['feed_view'])) {

        ?>
        <script>
            var map<?=$object->_id?> = L.map('map_<?=$object->_id?>', {
                touchZoom: false,
                scrollWheelZoom: false
            }).setView([<?=$object->lat?>, <?=$object->long?>], 16);
            var layer<?=$object->_id?> = new L.StamenTileLayer("toner-lite");
            map<?=$object->_id?>.addLayer(layer<?=$object->_id?>);
            var marker<?=$object->_id?> = L.marker([<?=$object->lat?>, <?=$object->long?>]);
            marker<?=$object->_id?>.addTo(map<?=$object->_id?>);
            //map<?=$object->_id?>.zoomControl.disable();
            map<?=$object->_id?>.scrollWheelZoom.disable();
            map<?=$object->_id?>.touchZoom.disable();
            map<?=$object->_id?>.doubleClickZoom.disable();
        </script>
    <?php
    }?>            
            
            <div style="display: none;">
                <p class="h-card vcard p-reviewer">
                    <a href="<?= $vars['object']->getOwner()->getURL(); ?>" class="icon-container">
                        <img class="u-logo logo u-photo photo" src="<?= $vars['object']->getOwner()->getIcon(); ?>"/>
                    </a>
                    <a class="p-name fn u-url url" href="<?= $vars['object']->getOwner()->getURL(); ?>"><?= $vars['object']->getOwner()->getName(); ?></a>
                    <a class="u-url" href="<?= $vars['object']->getOwner()->getURL(); ?>">
                        <!-- This is here to force the hand of your MF2 parser --></a>
                </p>
            </div>
</article>

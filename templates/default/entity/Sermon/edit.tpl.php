<?= $this->draw('entity/edit/header'); ?>
<?php

    $autosave = new \Idno\Core\Autosave();
    if (!empty($vars['object']->body)) {
        $body = $vars['object']->body;
    } else {
        $body = $autosave->getValue('sermon', 'bodyautosave');
    }
    if (!empty($vars['object']->title)) {
        $title = $vars['object']->title;
    } else {
        $title = $autosave->getValue('sermon', 'title');
    }
    if (!empty($vars['object']->sermonDate)) {
        $sermonDate = $vars['object']->sermonDate;
    } else {
        $sermonDate = $autosave->getValue('sermon', 'sermonDate');
    }
    if (empty($vars['object']->sermonDate)) {
		date_default_timezone_set("America/New_York"); 
        $sermonDate = date('l m-d-Y');
    }
	if (!empty($vars['object']->placename)) {
        $placename = $vars['object']->placename;
    } else {
        $placename = $autosave->getValue('sermon', 'placename');
    }
    if (!empty($vars['object']->churchurl)) {
        $churchurl = $vars['object']->churchurl;
    } else {
        $churchurl = $autosave->getValue('sermon', 'churchurl');
    }
     if (!empty($vars['object']->sermonURL)) {
        $sermonURL = $vars['object']->sermonURL;
    } else {
        $sermonURL = $autosave->getValue('sermon', 'sermonURL');
    }
	if (!empty($vars['object']->location)) {
        $placename = $vars['object']->location;
    } else {
        $placename = $autosave->getValue('sermon', 'location');
    }
	if (!empty($vars['object']->address)) {
        $address = $vars['object']->address;
    } else {
        $address = $autosave->getValue('sermon', 'address');
    }
    if (!empty($vars['object']->speaker)) {
        $speaker = $vars['object']->speaker;
    } else {
        $speaker = $autosave->getValue('sermon', 'speaker');
    }
    if (!empty($vars['object']->scripture)) {
        $scripture = $vars['object']->scripture;
    } else {
        $scripture = $autosave->getValue('sermon', 'scripture');
    }
    if (!empty($vars['object']->series)) {
        $series = $vars['object']->series;
    } else {
        $series = $autosave->getValue('sermon', 'series');
    }
    
    if (!empty($vars['object'])) {
        $object = $vars['object'];
    } else {
        $object = false;
    }

    /* @var \Idno\Core\Template $this */

?>
    <form action="<?= $vars['object']->getURL() ?>" method="post" enctype="multipart/form-data">

        <div class="row">

            <div class="col-md-8 col-md-offset-2 edit-pane">


                <?php

                    if (empty($vars['object']->_id)) {

                        ?>
                        <h4>Record Your Sermon Notes</h4>
                    <?php

                    } else {

                        ?>
                        <h4>Edit Sermon Notes</h4>
                    <?php

                    }

                ?>


                <div class="content-form">

                    <style>
                        .productCategory-block, .rating-block {
                            margin-bottom: 1em;
                        }
                    </style>
                    <label for="title">Sermon Title</label>
                    <input type="text" name="title" id="title" placeholder="What is the title of the sermon?" value="<?= htmlspecialchars($title) ?>" class="form-control"/>                    
                    
                	<label for="sermonDate">Sermon Series</label>
                    <input type="text" name="series" id="series" placeholder="What is name of the sermon series?" value="<?= htmlspecialchars($series) ?>" class="form-control"/>                    

					<label for="sermonDate">Sermon Date</label>
                    <input type="text" name="sermonDate" id="sermonDate" placeholder="What is the date this sermon?" value="<?= htmlspecialchars($sermonDate) ?>" class="form-control"/>                    

					<label for="speaker">Speaker</label>
                    <input type="text" name="speaker" id="speaker" placeholder="Who delivered the sermon?" value="<?= htmlspecialchars($speaker) ?>" class="form-control"/>                    
	
					<label for="scripture">Scripture Passage</label>
                    <input type="text" name="scripture" id="scripture" placeholder="What was the main scripture passage?" value="<?= htmlspecialchars($scripture) ?>" class="form-control"/>

					<label for="churchurl">Church Website Link</label>
                    <input type="text" name="churchurl" id="churchurl" placeholder="http://" value="<?= htmlspecialchars($churchurl) ?>" class="form-control"/>                    
	
					<label for="scripture">Sermon Audio Link</label>
                    <input type="text" name="sermonURL" id="sermonURL" placeholder="http://" value="<?= htmlspecialchars($sermonURL) ?>" class="form-control"/>
	
            <div id="geoplaceholder">
                <p style="text-align: center; color: #4c93cb;">
                    Hang tight ... searching for your location.
                </p>

                <div class="geospinner">
		    <div class="rect1"></div>
		    <div class="rect2"></div>
		    <div class="rect3"></div>
		    <div class="rect4"></div>
		    <div class="rect5"></div>
		</div>
            </div>
            <div id="geofields" class="map" style="display:none">
                <div class="geolocation content-form">

                    <p>
                        <label for="placename">
                            Location<br>
                        </label>
                        <input type="text" name="placename" id="placename" class="form-control" placeholder="Where are you?" value="<?= htmlspecialchars($vars['object']->placename) ?>" />
                        <input type="hidden" name="lat" id="lat" value="<?= $vars['object']->lat ?>"/>
                        <input type="hidden" name="long" id="long" value="<?= $vars['object']->long ?>"/>
                    </p>

                    <p>
                        <label for="user_address">Address<br>
                            <small>You can edit the address if it's wrong.</small>
                        </label>
                        <input type="text" name="user_address" id="user_address" class="form-control" value="<?= htmlspecialchars($vars['object']->address) ?>"/>
                        <input type="hidden" name="address" id="address" />
                    </p>

                    <div id="checkinMap" style="height: 250px" ></div>
                </div>
            </div>

				
					
                <label for="body">Notes</label>
                <?= $this->__([
                    'name' => 'body',
                    'value' => $body,
                    'object' => $object,
                    'wordcount' => true
                ])->draw('forms/input/richtext')?>
                <?= $this->draw('entity/tags/input'); ?>

				<?php echo $this->drawSyndication('article', $vars['object']->getPosseLinks()); ?>
                <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to" value="<?= \Idno\Core\site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
                
                <?= $this->draw('content/access'); ?>

                <p class="button-bar ">
	                
                    <?= \Idno\Core\site()->actions()->signForm('/sermon/edit') ?>
                    <input type="button" class="btn btn-cancel" value="Cancel" onclick="tinymce.EditorManager.execCommand('mceRemoveEditor',true, 'body'); hideContentCreateForm();"/>
                    <input type="submit" class="btn btn-primary" value="Publish"/>

                </p>

            </div>

        </div>
    </form>

  
    <div id="bodyautosave" style="display:none"></div>
<?= $this->draw('entity/edit/footer'); ?>

<script src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>IdnoPlugins/Sermon/sermon.js"></script>

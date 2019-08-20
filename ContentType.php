<?php

    namespace IdnoPlugins\Sermon {

        class ContentType extends \Idno\Common\ContentType {

            public $title = 'Sermon Notes';
            public $category_title = 'Sermon Notes';
            public $entity_class = 'IdnoPlugins\\Sermon\\Sermon';
            public $logo = '<i class="icon-align-left"></i>';
            public $indieWebContentType = array('article','Sermon');

        }

    }

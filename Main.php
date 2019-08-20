<?php

    namespace IdnoPlugins\Sermon {

        class Main extends \Idno\Common\Plugin {

            function registerPages() {
                \Idno\Core\site()->addPageHandler('/sermon/edit/?', '\IdnoPlugins\Sermon\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/sermon/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Sermon\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/sermon/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Sermon\Pages\Delete');
                \Idno\Core\site()->addPageHandler('/sermon/([A-Za-z0-9]+)/.*', '\Idno\Pages\Entity\View');
                \Idno\Core\site()->addPageHandler('/sermon/callback/?', '\IdnoPlugins\Sermon\Pages\Callback');
				
				\Idno\Core\site()->template()->extendTemplate('shell/head','sermon/head');
				}

            /**
             * Get the total file usage
             * @param bool $user
             * @return int
             */
            function getFileUsage($user = false) {

                $total = 0;

                if (!empty($user)) {
                    $search = ['user' => $user];
                } else {
                    $search = [];
                }

                if ($sermona = sermon::get($search,[],9999,0)) {
                    foreach($sermona as $sermon) {
                        /* @var review $review */
                        if ($sermon instanceof sermon) {
                            if ($attachments = $sermon ->getAttachments()) {
                                foreach($attachments as $attachment) {
                                    $total += $attachment['length'];
                                }
                            }
                        }
                    }
                }

                return $total;
            }

        }

    }

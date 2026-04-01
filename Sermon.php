<?php

    namespace IdnoPlugins\Sermon {

        use Idno\Core\Autosave;

        class Sermon extends \Idno\Common\Entity
        {

            function getTitle()
            {
                if (empty($this->title)) return 'Untitled';

                return $this->title;
            }

            function getDescription()
            {
                if (!empty($this->body)) return $this->body;

                return '';
            }

            function getSermonDate()
            {
                if (!empty($this->sermonDate)) return $this->sermonDate;

                return '';
            }

            function getPlaceName()
            {
                if (!empty($this->placename)) return $this->placename;

                return '';
            }

            function getLocation()
            {
                if (!empty($this->address)) return $this->address;

                return '';
            }

            function getSpeaker()
            {
                if (!empty($this->speaker)) return $this->speaker;

                return '';
            }

            function getSermonUrl()
            {
                if (!empty($this->sermonURL)) return $this->sermonURL;

                return '';
            }

            function getURL()
            {
                // If we have a URL override, use it
                if (!empty($this->url)) {
                    return $this->url;
                }

                if (!empty($this->canonical)) {
                    return $this->canonical;
                }

                if (!$this->getSlug() && ($this->getID())) {
                    return \Idno\Core\Idno::site()->config()->url . 'sermon/' . $this->getID() . '/' . $this->getPrettyURLTitle();
                } else {
                    return parent::getURL();
                }
            }

            function getScripture()
            {
                if (!empty($this->scripture)) return $this->scripture;

                return '';
            }

            function getSeries()
            {
                if (!empty($this->series)) return $this->series;

                return '';
            }

            function getChurchURL()
            {
                if (!empty($this->churchurl)) return $this->churchurl;

                return '';
            }

            /**
             * Sermon objects have type 'sermon'
             * @return string
             */
            function getActivityStreamsObjectType()
            {
                return 'sermon';
            }

            /**
             * Retrieve icon
             * @return mixed|string
             */
            function getIcon()
            {
                // Suppress libxml errors cleanly, then clear to avoid memory accumulation
                $previous = libxml_use_internal_errors(true);
                $doc      = \DOMDocument::loadHTML($this->getDescription());
                libxml_clear_errors();
                libxml_use_internal_errors($previous);

                if ($doc) {
                    $xpath = new \DOMXPath($doc);
                    $src   = $xpath->evaluate("string(//img/@src)");
                    if (!empty($src)) {
                        return $src;
                    }
                }

                return parent::getIcon();
            }

            function saveDataFromInput()
            {
                $new = empty($this->_id);

                $body = \Idno\Core\Idno::site()->currentPage()->getInput('body');

                if (!empty($body)) {

                    $this->body       = $body;
                    $this->title      = trim(\Idno\Core\Idno::site()->currentPage()->getInput('title'));
                    $this->sermonDate = trim(\Idno\Core\Idno::site()->currentPage()->getInput('sermonDate'));
                    $this->speaker    = trim(\Idno\Core\Idno::site()->currentPage()->getInput('speaker'));
                    $this->scripture  = trim(\Idno\Core\Idno::site()->currentPage()->getInput('scripture'));
                    $this->series     = trim(\Idno\Core\Idno::site()->currentPage()->getInput('series'));

                    // Validate URLs before saving
                    $sermonURL = trim(\Idno\Core\Idno::site()->currentPage()->getInput('sermonURL'));
                    if (!empty($sermonURL) && filter_var($sermonURL, FILTER_VALIDATE_URL)) {
                        $this->sermonURL = $sermonURL;
                    } elseif (!empty($sermonURL)) {
                        \Idno\Core\Idno::site()->session()->addErrorMessage('The sermon URL does not appear to be valid.');
                    }

                    $churchurl = trim(\Idno\Core\Idno::site()->currentPage()->getInput('churchurl'));
                    if (!empty($churchurl) && filter_var($churchurl, FILTER_VALIDATE_URL)) {
                        $this->churchurl = $churchurl;
                    } elseif (!empty($churchurl)) {
                        \Idno\Core\Idno::site()->session()->addErrorMessage('The church URL does not appear to be valid.');
                    }

                    // Location fields — optional, not required to save
                    $lat          = \Idno\Core\Idno::site()->currentPage()->getInput('lat');
                    $long         = \Idno\Core\Idno::site()->currentPage()->getInput('long');
                    $user_address = trim(\Idno\Core\Idno::site()->currentPage()->getInput('user_address'));
                    $placename    = trim(\Idno\Core\Idno::site()->currentPage()->getInput('placename'));

                    if (!empty($lat) && !empty($long)) {
                        $this->lat       = $lat;
                        $this->long      = $long;
                        $this->address   = $user_address;
                        $this->placename = $placename;
                    }

                    $access = \Idno\Core\Idno::site()->currentPage()->getInput('access');
                    $this->setAccess($access);

                    if ($time = \Idno\Core\Idno::site()->currentPage()->getInput('created')) {
                        if ($time = strtotime($time)) {
                            $this->created = $time;
                        }
                    }

                    // Handle photo upload on both new and edit
                    if (!empty($_FILES['photo']['tmp_name'])) {
                        if (\Idno\Entities\File::isImage($_FILES['photo']['tmp_name'])) {

                            // Extract EXIF data for rotation (JPEG only)
                            $exif = false;
                            if (function_exists('exif_read_data') && $_FILES['photo']['type'] == 'image/jpeg') {
                                try {
                                    $exif = exif_read_data($_FILES['photo']['tmp_name']);
                                    if ($exif) {
                                        // Exif may contain binary data unsafe for mongo
                                        $this->exif = base64_encode(serialize($exif));
                                    }
                                } catch (\Exception $e) {
                                    $exif = false;
                                }
                            }

                            if ($photo = \Idno\Entities\File::createFromFile($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $_FILES['photo']['type'], true, true)) {
                                $this->attachFile($photo);

                                // Generate thumbnails, with the option to override sizes via event
                                $sizes     = \Idno\Core\Idno::site()->events()->dispatch('photo/thumbnail/getsizes', new \Idno\Core\Event(array('sizes' => array('large' => 800, 'medium' => 400, 'small' => 200))));
                                $eventdata = $sizes->data();

                                foreach ($eventdata['sizes'] as $label => $size) {
                                    $filename = $_FILES['photo']['name'];
                                    if ($thumbnail = \Idno\Entities\File::createThumbnailFromFile($_FILES['photo']['tmp_name'], "{$filename}_{$label}", $size, false)) {
                                        $this->{"thumbnail_{$label}"}    = \Idno\Core\Idno::site()->config()->url . 'file/' . $thumbnail;
                                        $this->{"thumbnail_{$label}_id"} = substr($thumbnail, 0, strpos($thumbnail, '/'));
                                    }
                                }
                            }
                        } else {
                            \Idno\Core\Idno::site()->session()->addErrorMessage('This doesn\'t seem to be an image.');
                        }
                    }

                    if ($this->publish($new)) {

                        $autosave = new Autosave();
                        $autosave->clearContext('sermon');

                        // Only ping webmentions on public posts
                        if ($access == 'PUBLIC') {
                            \Idno\Core\Webmention::pingMentions(
                                $this->getURL(),
                                \Idno\Core\Idno::site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription())
                            );
                        }

                        return true;
                    }

                } else {
                    \Idno\Core\Idno::site()->session()->addErrorMessage('You can\'t save an empty entry.');
                }

                return false;
            }

            function deleteData()
            {
                // Only ping webmentions if this was a public post
                if ($this->getAccess() == 'PUBLIC') {
                    \Idno\Core\Webmention::pingMentions(
                        $this->getURL(),
                        \Idno\Core\Idno::site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription())
                    );
                }
            }

            /**
             * Given a latitude and longitude, reverse geocodes it into a structure
             * including name, address, city, etc via OpenStreetMap Nominatim
             *
             * @param float $latitude
             * @param float $longitude
             * @return array|bool
             */
            static function queryLatLong($latitude, $longitude)
            {
                $query         = self::getNominatimEndpoint() . "reverse?lat={$latitude}&lon={$longitude}&format=json&zoom=18";
                $http_response = \Idno\Core\Webservice::get($query)['content'];

                if (!empty($http_response)) {
                    $contents = json_decode($http_response);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($contents)) {
                        $response = [];
                        if (!empty($contents->address)) {
                            $addr             = (array) $contents->address;
                            $response['name'] = implode(', ', array_slice($addr, 0, 1));
                        }
                        if (!empty($contents->display_name)) {
                            $response['display_name'] = $contents->display_name;
                        }
                        // Only return if we actually got useful data
                        if (!empty($response)) {
                            return $response;
                        }
                    }
                }

                return false;
            }

            /**
             * Takes an address and returns OpenStreetMap data via Nominatim,
             * including latitude and longitude
             *
             * @param string $address
             * @param int $limit
             * @return array|bool
             */
            static function queryAddress($address, $limit = 1)
            {
                $query         = self::getNominatimEndpoint() . "search?q=" . urlencode($address) . "&format=json&limit={$limit}";
                $http_response = \Idno\Core\Webservice::get($query)['content'];

                if (!empty($http_response)) {
                    $decoded = json_decode($http_response, true);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($decoded) && is_array($decoded)) {
                        $contents              = $decoded[0]; // Take first result
                        $contents['latitude']  = $contents['lat'];
                        $contents['longitude'] = $contents['lon'];

                        return $contents;
                    }
                }

                return false;
            }

            /**
             * Returns the OpenStreetMap Nominatim endpoint to use.
             * Can be overridden via site config.
             *
             * @return string
             */
            static function getNominatimEndpoint()
            {
                if ($config = \Idno\Core\Idno::site()->config()->checkin) {
                    if (!empty($config['endpoint'])) {
                        return $config['endpoint'];
                    }
                }

                return 'https://nominatim.openstreetmap.org/';
            }

        }

    }
?>

<?php

/*
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 * 
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class OpenstreetmapEmbedder extends StudIPPlugin implements SystemPlugin {
    
    public function __construct()
    {
        parent::__construct();
        StudipFormat::addStudipMarkup("osmap", "\[osmap(.*?)\]", "\[\/osmap\]", "OpenstreetmapEmbedder::createMap");
        PageLayout::addHeadElement('script', array('type' => "text/javascript", 'src' => $this->getPluginURL()."/assets/khtml_all.js"), '');
    }
    
    public static function createMap($markup, $matches, $adress)
    {
        $id = "map_".uniqid();
        $params = explode(":", $matches[1]);
        foreach ($params as $param) {
            if ($param) {
                if (is_numeric($param)) {
                    $width = floor($param);
                }
            }
        }
        $width > 0 || $width = 400;
        $height = floor($width * 3 / 4);
        
        list($latitude, $longitude, $zoom) = explode(";", $adress);
        $output = sprintf(
                '<style>#%s > div:not(:first-child) {display: none;} </style>'.
                '<div id="%s" style="width: %spx; height: %spx; border: 1px solid black;"></div>' .
                '<script>
                jQuery(function () {
                    map=new khtml.maplib.base.Map(document.getElementById("%s"));
                    map.centerAndZoom(new khtml.maplib.LatLng(%s,%s),%s);
                    var zoombar=new khtml.maplib.ui.Zoombar();
                    map.addOverlay(zoombar);
                });
                </script>',
            $id,
            $id,
            $width,
            $height,
            $id,
            (double) $latitude,
            (double) $longitude,
            (int) $zoom
        );
        return str_replace("\n", "", $output);
    }
}


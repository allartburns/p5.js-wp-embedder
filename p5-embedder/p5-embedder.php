<?php
/**
 * Plugin Name: p5.js Embedder
 * Plugin URI: https://github.com/lmccart/p5.js/wiki/wordpress-embedder
 * Description: A brief description of the Plugin.
 * Version: 0.1.0
 * Author: Lauren McCarthy
 * Author URI: http://lauren-mccarthy.com
 * License: GPL2
 */

/*  Copyright 2014 Lauren McCarthy (email: hello@p5js.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined('ABSPATH') or die("No script kiddies please!");


add_action( 'wp_print_scripts', 'enqueue_scripts' );
add_action( 'wp_print_styles', 'enqueue_styles' );

function enqueue_scripts() {
  wp_enqueue_script( 'jquery', 'http://code.jquery.com/jquery-2.1.1.min.js');
  wp_enqueue_script( 'helper', plugins_url( 'helper.js', __FILE__ ));
}

function enqueue_styles() {
  wp_enqueue_style( 'style', plugins_url( 'style.css' , __FILE__ ));
}


function build_sketch($content) {
  $dom = new DOMDocument();
  $dom->loadHTML($content);
  $xpath = new DOMXpath($dom);
  $sketches = $xpath->query('//a[@class="p5-embed"]');

  foreach ($sketches as $s) {
    $name = $s->nodeValue;
    $url = $s->getAttribute('href');

    $code = file_get_contents($url);

    $w = $s->getAttribute('data-width');
    $h = $s->getAttribute('data-height');
    $nocode = $s->getAttribute('data-nocode');

    $iframe = $dom->createElement('iframe');
    $iframe->setAttribute('src', plugins_url('p5_iframe.html', __FILE__));
    $iframe->setAttribute('id', 'exampleFrame');
    $iframe->setAttribute('width', $w);
    $iframe->setAttribute('height', $h);
    $s->parentNode->appendChild($iframe);

    $pre = $dom->createElement('pre');
    $pre->setAttribute('class', 'language-javascript');
    $s->parentNode->appendChild($pre);

    $editor = $dom->createElement('code', $code);
    $editor->setAttribute('id', 'editor');
    $editor->setAttribute('class', 'language-javascript');
    $pre->appendChild($editor);

    if ($nocode == 'true') {
      $pre->setAttribute('style', 'display:none');
    }
  }

  return $dom->saveHTML();
}
add_filter('the_content', build_sketch);

?>
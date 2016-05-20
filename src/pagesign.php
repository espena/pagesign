<?php
  require_once( 'min/lib/Minify/HTML.php' );
  require_once( 'min/lib/Minify/CSS.php' );
  require_once( 'min/lib/JSMin.php' );  
  require_once( 'min/lib/Minify/Loader.php' ); 
  function main( $argc, $argv ) {
    if( $argv > 2 && file_exists( $argv[ 1 ] ) ) {
      $html = file_get_contents( $argv[ 1 ] );
      preg_match_all( '/<(link|script) .*?(href|src)=\"([^\"]+\.(css|js))\"[^>]*>(\s*<\/(link|script)>)?/i', $html, $m, PREG_SET_ORDER );
      foreach( $m as $tag ) {
        if( !file_exists( $tag[ 3 ] ) ) continue;
        if( $tag[ 1 ] == 'link' ) $tag[ 1 ] = 'style';
        $html = str_replace( $tag[ 0 ], '<' . $tag[ 1 ] . '>' . file_get_contents( $tag[ 3 ] ) . '</' . $tag[ 1 ] . '>', $html );
      }
      preg_match_all( '/(src=|href=|url\()(\s*)[\"\']([^\"\']{1,255})[\"\'](\s*\))?/i', $html, $m, PREG_SET_ORDER );
      $base64 = array();
      foreach( $m as $attr ) {
        if( !file_exists( $attr[ 3 ] ) ) continue;
        $lnEnding = ( $attr[ 1 ] == 'url(' ? "\\\n" : "\n" );
        $mime = mime_content_type( $attr[ 3 ] );
        $enc = "data:$mime;base64," . base64_encode( file_get_contents( $attr[ 3 ] ) );
        $html = str_replace( $attr[ 0 ], str_replace( $attr[ 3 ], trim( chunk_split( $enc, 8192, $lnEnding ), "\\\n" ), $attr[ 0 ] ), $html );
      }
      Minify_Loader::register(); 
      $html = Minify_HTML::minify( $html, array( 'cssMinifier' => array( 'Minify_CSS', 'minify' ), 'jsMinifier' => array( 'JSMin', 'minify' ) ) );
      $fpr = $argv[ 3 ];
      $keyinfo = str_replace( "\n", "\n  ", trim( `gpg --list-keys --with-fingerprint $fpr` ) ) . "\n";
      $header = $argc > 4 && file_exists( $argv[ 4 ] ) ? str_replace( '{keyinfo}', $keyinfo, file_get_contents( $argv[ 4 ] ) ) : $keyinfo;
      $gpg = gnupg_init();
      gnupg_addsignkey( $gpg, $fpr );
      $html = preg_split( '/<[\/ ]?html/', $html );
      $html = $html[ 0 ] . "<!--\n" . gnupg_sign( $gpg, $header . " -->\n<html" . $html[ 1 ] . "</html>\n<!--\n" ) . "-->";
      file_put_contents( $argv[ 2 ], $html );
    }
  }
  main( $argc, $argv );  
?>
<?php
  function main( $argc, $argv ) {
    if( $argv > 2 && file_exists( $argv[ 1 ] ) ) {
      $html = file_get_contents( $argv[ 1 ] );
      preg_match_all( '/(src=|href=|url\()(\s*)[\"\']([^\"\']{1,255})[\"\'](\s*\))?/i', $html, $m, PREG_SET_ORDER );
      $base64 = array();
      foreach( $m as $attr ) {
        if( !file_exists( $attr[ 3 ] ) ) continue;
        $lnEnding = ( $attr[ 1 ] == 'url(' ? "\\\n" : "\n" );
        $mime = mime_content_type( $attr[ 3 ] );
        $enc = "data:$mime;base64," . base64_encode( file_get_contents( $attr[ 3 ] ) );
        $html = str_replace( $attr[ 0 ], str_replace( $attr[ 3 ], trim( chunk_split( $enc, 8192, $lnEnding ), "\\\n" ), $attr[ 0 ] ), $html );
      }
      $fpr = $argv[ 3 ];
      $keyinfo = str_replace( "\n", "\n  ", trim( `gpg --list-keys --with-fingerprint $fpr` ) ) . "\n";
      $header = file_exists( $argv[ 4 ] ) ? str_replace( '{keyinfo}', $keyinfo, file_get_contents( $argv[ 4 ] ) ) : $keyinfo;
      $gpg = gnupg_init();
      gnupg_addsignkey( $gpg, $fpr );
      $html = preg_split( '/<[\/ ]?html/', $html );
      $html = $html[ 0 ] . "<!--\n" . gnupg_sign( $gpg, $header . " -->\n<html" . $html[ 1 ] . "</html>\n<!--\n" ) . "-->";
      file_put_contents( $argv[ 2 ], $html );
    }
  }
  main( $argc, $argv );  
?>
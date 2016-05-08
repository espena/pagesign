# pagesign

HTML page compiler and GPG signer. GPG Tools and the gnupg PHP extension must be installed on the system,
and a valid signing key must have been imported to the GPG keychain in order for this to work.

Embeds images and url('xxx') resources into the HTML page as base64 encoded data and signs the final file by
appending comment blocks before and after the &lt;html&gt; tags.

Please note that this application does not work on arbitrary HTML files. It is rather a tool for compiling special
purpose HTML source files to a static, signed single-source web page.

Usage:
```php pagesign.php source destination fingerprint comments```

_source_ : The source HTML file name.

_destination_ : The destination HTML file name.

_fingerprint_ : The fingerprint for the signing key. Must exist in GPG keychain.

_comments_ : Text template of the information comment block to be inserted immediately before the &lt;html&gt; start tag.
             The tag {keyinfo} can be used anywhere in this text file to output basic information of the signing key.

The entire content of the resulting HTML page, including image assets, can then be verified by anoyne using GPG.

License: [GPL Version 3, 29 June 2007](LICENSE.md) 
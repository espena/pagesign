# pagesign

HTML page compiler and GPG signer. GPG Tools and the gnupg PHP extension must be installed on the system,
and a valid signing key must have been imported to the GPG keychain in order for this to work.

Embeds images and url('xxx') resources into the HTML page as base64 encoded data and signs the final file by
appending comment blocks before and after the <html> tags.

Usage:
```php pagesign.php source destination fingerprint comments```

_source_ : The source HTML file name.

_destination_ : The destination HTML file name.

_fingerprint_ : The fingerprint for the signing key. Must exist in GPG keychain.

_comments_ : Text template of the information comment block to be inserted immediately before the <html> start tag.
             The tag {keyinfo} can be used anywhere in this text file to output basic information of the signing key.

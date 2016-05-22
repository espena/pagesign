# pagesign

HTML page compiler and PGP signer. GPG Tools and the gnupg PHP extension must be installed on the system,
and a valid signing key must have been imported to the GPG keychain in order for this to work.

Embeds images and url('xxx') resources into the HTML page as base64 encoded data and signs the final file by
appending comment blocks before and after the &lt;html&gt; tags.

Please note that this application does not work on arbitrary HTML files. It is a tool for compiling special
purpose HTML source files to a static, signed single-source web page.

Usage:
```php pagesign.php  source  destination  fingerprint  comments```

_source_ : The source HTML file name.

_destination_ : The destination HTML file name.

_fingerprint_ : The fingerprint for the signing key's public key. The (private) signing key must exist in GPG keychain.

_comments_ : Path to text file containing the template for the information block to be inserted immediately before
             the &lt;html&gt; start tag. The tag {keyinfo} can be used anywhere in this text file to output basic
             information of the signing key.

The entire content of the resulting HTML page, including image assets, can then be verified by anoyne using GPG.

##Example

The HTML file _source.html_ points to ``my_styles.css`` in a CSS link tag, uses ``my_functions.js`` as source for
a script tag and embeds ``my_logo.jpg`` in an image tag. All files are located locally in the same directory.

```html
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>My signed web page</title>
    <link rel="stylesheet" type="text/css" href="my_styles.css" />
  </head>
  <body>
    <h1>My signed web page</h1>
    <p>
      The content of this site is signed with PGP.
      <img src="my_logo.jpg" />
    </p>
    <script type="text/javascript" src="my_functions.js" />
  </body>
</html>
```

Assuming that ``/path/to/pagesign.php`` is where the pagesign script is located, and the private key corresponding to pubkey
with fingerprint ``BA7EAC9F78DAC483B0C7DE79DD32F64A341F8E25`` is already imported to your GPG keychain, the command

```shell
php /path/to/pagesign.php  source.html  destination.html  BA7EAC9F78DAC483B0C7DE79DD32F64A341F8E25
```

will create a file named _destination.html_. You will be prompted for your private key's password if applicable.
The destination file will contain something similar to this (the base64 encoded image string is removed):

```html
<!doctype html><!--
-----BEGIN PGP SIGNED MESSAGE-----
Hash: SHA512

pub   4096R/341F8E25 2015-09-04
        Key fingerprint = BA7E AC9F 78DA C483 B0C7  DE79 DD32 F64A 341F 8E25
  uid       [  full  ] Espen Andersen (please verify that this is the latest version) <post@espenandersen.no>
  sub   4096R/81FA3608 2015-09-04
 -->
<html
lang="en"><head><meta
charset="utf-8"><title>My signed web page</title><style>h1{font-color:#00a0cd}</style></head><body><h1>My signed web page</h1><p>
The content of this site is signed with PGP.
<img src="data:image/jpeg;base64, ... " /></p> <script>function my_alert(){alert('Just an alert script');}</script> </body></html>
<!--
-----BEGIN PGP SIGNATURE-----
Comment: GPGTools - https://gpgtools.org

iQIcBAEBCgAGBQJXPrg6AAoJEN0y9ko0H44lRXIP/jFEXenTFd6gl7u7MMg/8tWZ
XyHdc5RZmctij61dbV70qmzuP6a2KsR6SrpES5/gBBu1ZQLKh41W4a5SvE4JwZng
FboFyoQ05ACx5DIdEo95vWDEYKX+NQOaOGbzrBdTl7yTkQwSw9OLBv4MiUvy7X4g
jAeeg3vxUJ2gHy2Ib6f7UEdZOq9blyVMdJbNPyH337NWy9wpcrJkiJefGIOBEial
UNaUIePx8FLrQnH5jzaiAtDPawKwjWaQS4WnZv3y3pHMSl1bRWSWO14GAbsfu5l+
krQxyo7xMKP5fOteokSoI0EDNDgakkOANvAZwTRGO1ObdLx+n7Llaiyzjv9iWmcU
pGh0s6ejC82Mzdh1n50DWnWbApgOLjDcNek++gXQUx+UB3DG5b6sdJOjNtErT+SI
tBfzks9Z6Tz3p0M8aRmI0x534ZL25Tj2nFf3gsv9Xy2JzX98SOJsTyrcj5Tx5GGW
X4USYCKDrNGtUkUeijYUSfQ6q//Dj8/T02S/Wft0LR/XhZWu2WtG+g27523F+8qp
ELWJmhw6bluKPRlUWvciQe4AihZg24obqolCqiWr7TiK+mxokzNXQtVC79rQ2f2U
ttDw50b79jVwr4WLshXrTtrQ8EVfh8yscRbqhKUbg63OYYHBDW+0jWY7ruRxQsfn
KToPQRBF/9IDI2cdAGrd
=UT36
-----END PGP SIGNATURE-----
-->
```

On Linux/OSX, the signature may then be verified by running

```shell
gpg --verify destination.html
```

or, if it is located on a web server:

```shell
curl https://url.to/destination.html | gpg > /dev/null
```

## Installation

It might be worth the effort to make a shorthand script and put it in one of your system's binary directories. 
The script is very simple:

```shell
#!/bin/bash
$(which php) -q /projects/pagesign/src/pagesign.php $1 $2 $3 $4
```

Change ``/projects/pagesign/src`` to the correct path to ``pagesign.php`` on your system. Name the file ``pagesign``
and set correct permissions. The file should be owned by root and made executable. Move it over to one of the
directories enlisted in your $PATH environment variable, ``/usr/local/bin`` is a good place.

Afther this, you can call pagesign from anywhere, using this syntax:

```shell
pagesign  source  destination  fingerprint  [comments]
```

## Licensing

Pagesign is written by Espen Andersen, and released under the [GNU General Public License](http://www.gnu.org/licenses/gpl.txt).

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

Although the author has attempted to find and correct any bugs in the free software programs, the author is not responsible
for any damage or losses of any kind caused by the use or misuse of the programs. The author is under no obligation to provide
support, service, corrections, or upgrades to the free software programs.

### Gnupg

Pagesign uses the [PHP gnupg](https://pecl.php.net/package/gnupg) extension, maintained by Jim Jagielski and Sean DuBois.

Copyright (c) Jim Jagielski <jimjag@php.net>

Copyright (c) Sean DuBois <sean@siobud.com>

All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

<ul>
<li>Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.</li>
<li>Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.</li>
<li>Neither the name of this project nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.</li>
</ul>

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

### Minify

Pagesign uses [PHP Minify](https://github.com/mrclay/minify), written by Ryan Grove and Steve Clay.

Copyright (c) 2008 Ryan Grove <ryan@wonko.com>

Copyright (c) 2008 Steve Clay <steve@mrclay.org>

All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

<ul>
<li>Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.</li>
<li>Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.</li>
<li>Neither the name of this project nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.</li>
</ul>

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
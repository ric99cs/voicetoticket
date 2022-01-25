# voicetoticket
A script that is using Microsofts speech recognition service, to create tickets in Zammad coming from a voicemail. This one is tested with FreePBX/Asterisk.

At least you need an azure access token from Microsofts speech to text service. For use with zammad to create tickets from voicemail, you also need an acces token from your zammad system.

Copy all .php files to your asterisk agi directory. (Typically /usr/share/asterisk/agi-bin)
Edit the config.php with your tokens and endpoints. If you want to use only voicetomail, then you don't need to fill the zammad parts. Make the config.php readable only by asterisk using chown & chmod 600.

If not available, install the module "custom destinations" on your FreePBX install. Copy the content of extensions_custom.conf to your conf file in /etc/asterisk. Copy the one you need. (Or both with different namings, if you want to use multiple routes)

Create a new custom destination on your FreePBX, destination should be for instance "custom-ms-speechrecog,s,1". Change your inbound route, so that the final destination leads to the custom destination. The recorded files are stored in /tmp, not in the FreePBX voicemail system, so they are not accessable by UCP or via phone.

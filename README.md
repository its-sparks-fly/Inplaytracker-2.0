# RPG-Inplaytracker<br />
Mit diesem Plugin habt ihr und eure User die Möglichkeit, euer Inplay besser im Auge zu behalten. Es kommt mit ein paar Funktionen, die ich euch hier stichpunktartig näher vorstellen möchte:

<ul>
<li> Taggen seiner Mitspieler
<li> Neues Feld "Inplay-Datum" zur Sortierung von Szenen
<li> Auflistung & Verlinkung der Mitspieler in der Themenuebersicht (forumdisplay.php)
<li> Private Nachricht bei neu erstellter Szenen
<li> Private Nachricht bei neuer Antwort auf Inplayszene
<li> Uebersicht aller aktiver Szenen mit ausstehenden Posts (aller Charaktere)
<li> Festlegen einer Posting-Reihenfolge (wird bei Übersicht offener Posts beachtet)
<li> Anzeige der Gesamtanzahl (offener) Szenen im Header
<li>  Szenentracker im Profil mit Zählung von Posts / sortiert nach Datum (auch Archiv)
</ul>

<h1>Plugin funktionsfähig machen</h1>
<ul>
<li>Die Plugin-Datei ladet ihr in den angegebenen Ordner <b>inc/plugins</b> hoch.
<li>Das Plugin muss nun im Admin CP unter <b>Konfiguration - Plugins</b> installiert und aktiviert werden
<li>In den Foreneinstellungen findet ihr nun - ganz unten - Einstellungen zu "Inplaytracker". Gebt dort die entsprechenden Kategorie/Foren-IDs an.
</ul><br />

Das Plugin ist nun einsatzbereit. Solltet ihr schon einiges an eurem Forum gemacht haben, und nicht wie ich im Testdurchlauf ein Default-Theme verwenden, kann es sein, dass nicht alle Variablen eingefügt werden. Sollte euch eine Anzeige fehlen, könnt ihr auf folgende Variablen zurückgreifen:

<blockquote>{$menu_inplaytracker}  // Link zur Übersicht der Szenen (header)
* ruft header_inplaytracker auf

{$inplaytracker} // Szenentracker im Profil (member_profile)
* ruft member_profile_inplaytracker auf

{$tracker_partners} // Eingabefeld für Postingpartner (newthread, editpost)
* ruft newthread_ip_partners auf

{$tracker_date} // Eingabefeld für Postingpartner (newthread, editpost)
* ruft newthread_ip_date auf</blockquote>

<h1>Template-Änderungen</h1>
Folgende Templates werden durch dieses Plugin <i>neu hinzugefügt</i>:
<ul>
<li>header_inplaytracker
<li>member_profile_inplaytracker
<li>member_profile_inplaytracker_bit
<li>misc_inplaytracker
<li>misc_ip_bit
<li>misc_ip_user
<li>newthread_ip_date
<li>newthread_ip_partners
</ul>

Folgende Templates werden durch dieses Plugin <i>bearbeitet</i>:
<ul>
<li>header
<li>member_profile
<li>newthread
<li>editpost
</ul>

<h1>Demo</h1><br />
<center>

<img src="http://fs5.directupload.net/images/161221/9ysr3bth.png" /><br />
http://fs5.directupload.net/images/161221/9ysr3bth.png<br /><br />

<img src="http://fs5.directupload.net/images/161221/ycpw6xsw.png" /><br />
http://fs5.directupload.net/images/161221/ycpw6xsw.png<br /><br />

<img src="http://fs5.directupload.net/images/161221/uudsxgrd.png" /><br />
http://fs5.directupload.net/images/161221/uudsxgrd.png<br /><br />

<img src="http://fs5.directupload.net/images/161221/j4hjluj7.png" /><br />
http://fs5.directupload.net/images/161221/j4hjluj7.png<br /><br />

<img src="http://fs5.directupload.net/images/161221/kuw4obai.png" /><br />
http://fs5.directupload.net/images/161221/kuw4obai.png<br /><br />

<img src="http://fs5.directupload.net/images/161221/es4gr2wp.png" /><br />
http://fs5.directupload.net/images/161221/es4gr2wp.png<br />
(Ansicht für Charakter ohne Zweitcharaktere)<br /><br />

<img src="http://fs5.directupload.net/images/161221/bdrolucv.png" /><br />
http://fs5.directupload.net/images/161221/bdrolucv.png<br />
(Charakter mit Zweitcharakteren)<br /><br />

</center>

Lokal unter MyBB 1.8.8 getestet und es funktionierte alles - da es allerdings nicht im alltäglichen Forengebrauch zum Test kam, kann es natürlich sein, dass es die ersten Härtetests aus diversen Gründen nicht besteht. Sollte es zu Fehlermeldungen oder Problemen kommen, könnt ihr euch jederzeit <b><u>hier im Thread</u></b> melden.


<h1>Fehlerbehebungen</h1>
<ul>
<li> PNs werden bei Antworten nicht nur im Inplay-Bereich verschickt (in neuem Download behoben)
<li> <a href="http://storming-gates.de/showthread.php?tid=20553&pid=144205#pid144205" target="blank">Link zum Ersteller des letzten Beitrags führt im Tracker zu falschem Profil</a> (Link zum Bugfix / behoben)[/list]
</ul>

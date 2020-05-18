<?php
// E-posta bağlantı //
$sunucu = '{imap.gmail.com:993/imap/ssl}INBOX';
$eposta = 'Buraya Gmail adresinizi yazınız.';
$sifre = 'Buraya Gmail adresinizin şifresini yazınız.';

// E-posta bağlantısı //
$gelenkutusu = imap_open($sunucu, $eposta, $sifre) or die('Gmail hesabına bağlantı kurulamadı: ' . imap_last_error());

// Hangi banka //
$banka = "Enpara"; // Bankaya göre değiştiriniz.
// Enparadan gelen ve okunmayan e-postalar //
$emails = imap_search($inbox, 'UNSEEN From ' . $banka);

if ($emails)
{
    rsort($emails);
    foreach ($emails as $mail_numarasi)
    {
        $headerInfo = imap_headerinfo($gelenkutusu, $mail_numarasi);
        $structure = imap_fetchstructure($gelenkutusu, $mail_numarasi);
        $overview = imap_fetch_overview($gelenkutusu, $mail_numarasi, 0);
        $message = imap_qprint(imap_fetchbody($gelenkutusu, $mail_numarasi, 1));
        preg_match('#Tutar(.*?)TL</span></p></td>#si', $message, $degisken); // Enpara için yazılmıştır, harici bir banka için isterseniz bu kısmı düzenleyiniz //
        preg_match('#klama(.*?)<td style="width: 3.3000002px#si', $message, $degisken2); // Enpara için yazılmıştır, harici bir banka için isterseniz bu kısmı düzenleyiniz //
        preg_match('#Ad/ unvan(.*?)<tr valign="top">#si', $message, $degisken3); // Enpara için yazılmıştır, harici bir banka için isterseniz bu kısmı düzenleyiniz //
        $gonderen = trim(strip_tags($degisken3[1]));
        $gonderen = str_replace(":", "", $gonderen);

        $aciklama = trim(strip_tags($degisken2[1]));
        $aciklama = str_replace(":", "", $aciklama);

        $tutar = trim(strip_tags($degisken[1]));
        $tutar = str_replace(":", "", $tutar);
        $tutar = explode(",", $tutar);
        $tutar = $tutar[0];

        if (strstr($aciklama, 'BORC-SC'))
        {
            $userid = explode("SC", $aciklama);
            $userid = $userid[1];
            // Ödeme başarılı ise, $userid havale kısmında açıklamaya yazılan ID numarasını verir. //
            // $tutar yatırılan tutardır. (Virgül sonrasını almaz.) //
            // $gonderen gönderen kişinin isim ve soyismidir. //
            
        }

        // E-posta okundu olarak işaretlenir, işlem biter //
        $status = imap_setflag_full($gelenkutusu, $overview[0]->msgno, "\Seen \Flagged");
    }
}

imap_close($gelenkutusu);
?>

<?php

$place = $_POST["placeSearched"] ?? "";
$nomiUrl = "http://localhost/nominatim/";

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL,$nomiUrl . "search?q=" . urlencode($place) . "&format=json&addressdetails=true&limit=16&accept-language=de&dedupe=true");
curl_setopt($curl, CURLOPT_TIMEOUT, 10);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);

$content = curl_exec($curl);
$status = curl_getinfo($curl,CURLINFO_HTTP_CODE);
$encoded_content = json_encode($content);
curl_reset($curl);

if($status >= 400){
  header("Location: noNominatim.html?error=" . urlencode($status));
};

if($content == "[]"){
    header("Location: faildSearch.html?place=" . urlencode($place));
};

?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find a Place</title>

  <link rel="stylesheet" href="style.css">
</head>
<body>
    <center><h1>Ergebnis der Suche zu <strong id="place"></strong></h1></center>
    <tabel id="results">

    </tabel>
    <form action="index.html" method="post" class="place_card">
        <button type="submit">Zurück zur Suche</button>
    </form>
    <script>
        var place = "<?php echo $place; ?>";
        document.getElementById("place").textContent = place;
        var data = <?php echo $content; ?>;
        console.log(data)
        var results = document.getElementById("results");
        for (var i = 0; i < data.length; i++){
            if (data[i]["addresstype"] == "statistical" || data[i]["addresstype"] == "railway" || data[i]["type"] == "administrative"){
                console.log(data[i]["addresstype"])
            }else if (data[i]["addresstype"] == "hamlet"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>${data[i].name}:</h1>
                    <h2>Art des Ortes: Kleine Siedlung</h2>
                    <h2>Postleitzahl: ${data[i]["address"].postcode}</h2>
                    <h2>Dorf/Stadt: ${data[i]["address"].village || data[i]["address"].town ||"Nicht bekannt"}</h2>
                    <h2>Landkreis: ${data[i]["address"].county}</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <a href = "https://www.google.com/maps/@${data[i].lat},${data[i].lon},5000m/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if(data[i]["addresstype"] == "village"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>${data[i].name}:</h1>
                    <h2>Art des Ortes: Dorf</h2>
                    <h2>Postleitzahl: ${data[i]["address"].postcode}</h2>
                    <h2>Stadt: ${data[i]["address"].town}</h2>
                    <h2>Landkreis: ${data[i]["address"].county}</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2> 
                    <a href = "https://www.google.com/maps/place/${data[i].name}/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if(data[i]["addresstype"] == "town"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>${data[i].name}</h1>
                    <h2>Art des Ortes: Stadt</h2>
                    <h2>Postleitzahl: ${data[i]["address"].postcode}</h2>
                    <h2>Landkreis: ${data[i]["address"].county}</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <a href = "https://www.google.com/maps/place/${data[i].name}/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if(data[i]["addresstype"] == "city"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>${data[i].name}</h1>
                    <h2>Art des Ortes: Großstadt</h2>
                    <h2>Postleitzahl: ${data[i]["address"].postcode || "Nicht bekannt"}</h2>
                    <h2>Landkreis: ${data[i]["address"].county}</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <a href = "https://www.google.com/maps/place/${data[i].name}/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if(data[i]["addresstype"] == "county"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>${data[i].name}</h1>
                    <h2>Art des Ortes: Landkreis</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <a href = "https://www.google.com/maps/place/${data[i].name}/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if(data[i]["addresstype"] == "state"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>${data[i].name}</h1>
                    <h2>Art vom Ort: Bundesland</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <a href = "https://www.google.com/maps/place/${data[i].name}/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if(data[i]["addresstype"] == "country"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>${data[i].name}</h1>
                    <h2>Art vom Ort: Land</h2>
                    <a href = "https://www.google.com/maps/place/${data[i].name}/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if(data[i]["addresstype"] == "building" || data[i]["addresstype"] == "shop" || data[i]["addresstype"] == "office" || data[i]["addresstype"] == "commercial" || data[i]["addresstype"] == "amenity" || data[i]["type"] == "tower"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>Name: ${data[i].name}</h1>
                    <h2>Art des Ortes: Gebäude</h2>
                    <h2>Postleitzahl: ${data[i]["address"].postcode || "Nicht vorhanden"}</h2>
                    <h2>Stadtteil: ${data[i]["address"].city_district || "Nicht vorhanden"}</h2>
                    <h2>Dorf/Stadt: ${data[i]["address"].village || data[i]["address"].town ||data[i]["address"].city}</h2>
                    <h2>Straße: ${data[i]["address"].road || "Nicht vorhanden"}</h2>
                    <h2>Hausnummer: ${data[i]["address"].house_number || "Nicht vorhanden"}</h2>
                    <h2>Landkreis: ${data[i]["address"].county || "Nicht vorhanden"}</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <h2>Adresse: ${data[i]["address"].road || ""} ${data[i]["address"].house_number || ""} ${data[i]["address"].postcode || ""}  ${data[i]["address"].town || data[i]["address"].village || data[i]["address"].city}</h2>
                    <a href = "https://www.google.com/maps/@${data[i].lat},${data[i].lon},90m/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if(data[i]["type"] == "ambulance_station"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>Name: ${data[i].name}</h1>
                    <h2>Art des Ortes: Notaufnahme</h2>
                    <h2>Postleitzahl: ${data[i]["address"].postcode || "Nicht vorhanden"}</h2>
                    <h2>Stadtteil: ${data[i]["address"].city_district || "Nicht vorhanden"}</h2>
                    <h2>Dorf/Stadt: ${data[i]["address"].village || data[i]["address"].town ||data[i]["address"].city}</h2>
                    <h2>Straße: ${data[i]["address"].road || "Nicht vorhanden"}</h2>
                    <h2>Hausnummer: ${data[i]["address"].house_number || "Nicht vorhanden"}</h2>
                    <h2>Landkreis: ${data[i]["address"].county || "Nicht vorhanden"}</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <h2>Adresse: ${data[i]["address"].road || ""} ${data[i]["address"].house_number || ""} ${data[i]["address"].postcode || ""}  ${data[i]["address"].town || data[i]["address"].village || data[i]["address"].city}</h2>
                    <a href = "https://www.google.com/maps/@${data[i].lat},${data[i].lon},90m/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if(data[i]["addresstype"] == "emergency"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>Name: ${data[i].name || data[i].type}</h1>
                    <h2>Art des Ortes: Notfall hilfen</h2>
                    <h2>Art der Notfall hilfe: ${data[i].type}</h2>
                    <h2>Postleitzahl: ${data[i]["address"].postcode}</h2>
                    <h2>Dorf/Stadt: ${data[i]["address"].village || data[i]["address"].town ||data[i]["address"].city}</h2>
                    <h2>Straße: ${data[i]["address"].road}</h2>
                    <h2>Hausnummer: ${data[i]["address"].house_number || ""}</h2>
                    <h2>Landkreis: ${data[i]["address"].county}</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <h2>Adresse: ${data[i]["address"].road} ${data[i]["address"].house_number} ${data[i]["address"].postcode}  ${data[i]["address"].town || data[i]["address"].village || data[i]["address"].city} ${data[i]["address"].city_district || ""}</h2>
                    <a href = "https://www.google.com/maps/@${data[i].lat},${data[i].lon},50m/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if (data[i]["addresstype"] == "city_district"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>${data[i].name}:</h1>
                    <h2>Art des Ortes: Stadtteil</h2>
                    <h2>Stadt: ${data[i]["address"].city}</h2>
                    <h2>Landkreis: ${data[i]["address"].county}</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <a href = "https://www.google.com/maps/place/${data[i].name}/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if (data[i]["addresstype"] == "neighbourhood"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>${data[i].name}:</h1>
                    <h2>Art des Ortes: Nachbarschaft</h2>
                    <h2>Stadtteil: ${data[i]["address"].suburb || "Nicht vorhanden"}</h2>
                    <h2>Stadt: ${data[i]["address"].city || data[i]["address"].town}</h2>
                    <h2>Landkreis: ${data[i]["address"].county}</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <a href = "https://www.google.com/maps/place/${data[i].name}/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if (data[i]["addresstype"] == "suburb"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>${data[i].name}:</h1>
                    <h2>Art des Ortes: Wohngebiet</h2>
                    <h2>Stadtteil: ${data[i]["address"].city_district}</h2>
                    <h2>Stadt: ${data[i]["address"].city}</h2>
                    <h2>Landkreis: ${data[i]["address"].county}</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <a href = "https://www.google.com/maps/place/${data[i].name}/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if (data[i]["addresstype"] == "river"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>${data[i].name}:</h1>
                    <h2>Art des Ortes: Fluss</h2>
                    <h2>Landkreis: ${data[i]["address"].county || "Nicht vorhanden"}</h2>
                    <h2>Bundesland: ${data[i]["address"].state || "Nicht vorhanden"}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <a href = "https://www.google.com/maps/@${data[i].lat},${data[i].lon},50m/">Auf Google Maps zeigen</a></td>
                <section> 
                <tr>`
            }else if (data[i]["type"] == "bus_stop"){
                var row = `<tr>
                <section class="place_card"> 
                    <h1>Name: ${data[i].name}</h1>
                    <h2>Art des Ortes: Bushaltestelle</h2>
                    <h2>Postleitzahl: ${data[i]["address"].postcode || "Nicht vorhanden"}</h2>
                    <h2>Stadtteil: ${data[i]["address"].city_district || "Nicht vorhanden"}</h2>
                    <h2>Dorf/Stadt: ${data[i]["address"].village || data[i]["address"].town ||data[i]["address"].city}</h2>
                    <h2>Straße: ${data[i]["address"].road || "Nicht vorhanden"}</h2>
                    <h2>Landkreis: ${data[i]["address"].county || "Nicht vorhanden"}</h2>
                    <h2>Bundesland: ${data[i]["address"].state}</h2>
                    <h2>Land: ${data[i]["address"].country}</h2>
                    <a href = "https://www.google.com/maps/@${data[i].lat},${data[i].lon},90m/">Auf Google Maps zeigen</a></td>
                <section
                <tr>`
            }else {
                var row = `<tr>
                <section class="place_card"> 
                    <h1>Name: ${data[i].name}</h1>
                    <h2>Kategory: ${data[i].addresstype}</h2>
                    <h2>Art des Ortes: ${data[i].type}</h2>
                    <h2>Postleitzahl: ${data[i]["address"].postcode || "Nicht vorhanden"}</h2>
                    <h2>Land: ${data[i]["address"].country|| "Nicht vorhanden"}</h2>
                    <a href = "https://www.google.com/maps/@${data[i].lat},${data[i].lon},50m/">Auf Google Maps zeigen</a></td>
                <section>
                <tr>`
            }
            results.innerHTML += row
        }
    </script>
</body>
</html>

                
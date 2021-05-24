<?php
    require_once '../tools/dbconfig.php';
    
    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
    $search=mysqli_real_escape_string($conn, $_GET["search"]);
    $query="SELECT m.id as id_museo, m.nome as nome_museo, m.lat as latitudine_museo, m.lon as longitudine_museo, t.tipo as tipo_museo,
        m.costo_biglietto as costo_biglietto, m.data_apertura as data_apertura, m.immagine_museo as immagine_museo, m.introduzione as introduzione,
        mp.museo as id_museo_privato, c.comune as comune, c.provincia as provincia, c.regione as regione
        FROM museo m join citta c on m.citta=c.id join tipo_museo t on m.tipo=t.id left join museo_privato mp on m.id=mp.museo
        ".(!empty($search) ? " WHERE nome like '%$search%'" : "")."
        ORDER BY nome
    ";
    $res=mysqli_query($conn, $query);
    if(!$res)
        echo mysqli_error($conn);

    $result=array();
    while($row = mysqli_fetch_object($res)){
        $result[]=array(
            "id"=>$row->id_museo,
            "name"=>$row->nome_museo,
            "type"=>$row->tipo_museo,
            "idMuseoPrivato"=>$row->id_museo_privato,
            "city"=>$row->comune,
            "provincia"=>$row->provincia,
            "description"=>$row->introduzione,
            "image"=>$row->immagine_museo,
            "coordinate"=>array("lat"=>$row->latitudine_museo,"lon"=>$row->longitudine_museo)
        );
    }
    mysqli_free_result($res);
    
    mysqli_close($conn);

    echo json_encode($result);
?>
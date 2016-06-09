<?php 
include('_header.php'); 


echo "<h2>Efetuar Matching</h2>";
echo "<h2>Upload Arquivo Excell</h2>";
echo "<h2>Upload Arquivo XML</h2>";

#aqui somente quem ja foi match
$q = "
    select 
    jobs.job_id,jobs.company_id,jobs.start,jobs.end,jobs.titulo_label,
    edus.edu_id,edus.major_id,edus.school_id,edus.ano1,edus.ano2,
    alumni.alumni_id,alumni.aniversario,alumni.conexoes,alumni.img,alumni.nome,alumni.hash
    from 
    jobs,edus,alumni
    inner join matching on alumni.alumni_id=matching.alumni_id 
    where alumni.alumni_id=jobs.alumni_id
    and alumni.alumni_id=edus.alumni_id
    order by alumni.nome
    ;";

$result = $cn->query($q);
#var_dump($result);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

foreach($rows as $row){
    extract($row);
    #echo $nome,' - ',$job_id,$img;


}


include('_footer.php');
<?php
    include ('header.php');
    include('../config/variables.php');
    include('../config/conexion.php');
?>

<title><?=$tit;?></title>
<meta name="author" content="Luigi Pérez Calzada (GianBros)" />
<meta name="description" content="Descripción de la página" />
<meta name="keywords" content="etiqueta1, etiqueta2, etiqueta3" />
<!-- <link href="../assets/css/login.css" rel="stylesheet"> -->
<?php
    include ('navbar.php');
    if (!isset($_SESSION['sessU'])){
        echo '<div class="row><div class="col-sm-12 text-center"><h2>No tienes permiso para entrar a esta sección. ━━[○･｀Д´･○]━━ </h2></div></div>';
    }else {
        //Obtenemos Nombre del nivel
        $idNivel = $_GET['idNivel'];
        $sqlGetName = "SELECT nombre FROM $tNivEsc WHERE id='$idNivel' ";
        $resGetName = $con->query($sqlGetName);
        $rowGetName = $resGetName->fetch_assoc();
        $nameNivel = $rowGetName['nombre'];
        //Obtenemos Nombre del grado
        $idGrado = $_GET['idGrado'];
        $sqlGetName = "SELECT nombre FROM $tGrado WHERE id='$idGrado' ";
        $resGetName = $con->query($sqlGetName);
        $rowGetName = $resGetName->fetch_assoc();
        $nameGrado = $rowGetName['nombre'];
        //Obtenemos Materia
        $idMateria = $_GET['idMateria'];
        $sqlGetNameMateria = "SELECT nombre FROM $tMat WHERE id='$idMateria' ";
        $resGetNameMateria = $con->query($sqlGetNameMateria);
        $rowGetNameMateria = $resGetNameMateria->fetch_assoc();
        $nameMateria = $rowGetNameMateria['nombre'];
        //Obtenemos Bloque
        $idBloque = $_GET['idBloque'];
        $sqlGetNameBloque = "SELECT nombre FROM $tBloq WHERE id='$idBloque' ";
        $resGetNameBloque = $con->query($sqlGetNameBloque);
        $rowGetNameBloque = $resGetNameBloque->fetch_assoc();
        $nameBloque = $rowGetNameBloque['nombre'];
    
?>

    <div class="container">
        <div class="row placeholder text-center">
            <div class="col-sm-12 placeholder">
                <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalAdd">
                    Añadir nuevo Tema
                    <span class="glyphicon glyphicon-plus"></span>
                </button>
            </div>
        </div>
        <br>
        <div class="table-responsive">
            <table class="table table-striped" id="data">
                <caption>
                    <?php 
                        $cadCap = '<a href="admin_add_banco_niveles.php">'.$nameNivel.'</a> -> ';
                        $cadCap .= '<a href="admin_add_banco_grados.php?idNivel='.$idNivel.'">'.$nameGrado.'</a> -> ';
                        $cadCap .= '<a href="admin_add_banco_materias.php?idNivel='.$idNivel.'&idGrado='.$idGrado.'">'.$nameMateria.'</a> -> ';
                        $cadCap .= '<a href="admin_add_banco_bloques.php?idNivel='.$idNivel.'&idGrado='.$idGrado.'&idBloque='.$idBloque.'">'.$nameBloque.'</a>';
                    ?>
                    <?= $cadCap; ?> -> Temas
                </caption>
                <thead>
                    <tr>
                        <th><span title="id">Id</span></th>
                        <th><span title="nombre">Nombre</span></th>
                        <th><span title="created">Creado</span></th>
                        <th>Ver Subtemas</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        
        <div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labellebdy="myModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                        </button>
                        <h4 class="modal-title" id="exampleModalLabel">Añadir nuevo tema al bloque: <?=$nameBloque;?></h4>
                        <p class="msgModal"></p>
                    </div>
                    <form id="formAdd" name="formAdd">
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="inputIdBloque" value="<?= $idBloque; ?>" >
                                <label for="inputName">Nombre: </label>
                                <input type="text" class="form-control" id="inputName" name="inputName" >
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Añadir</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('#loading').hide();
        var ordenar = '';
        $(document).ready(function(){
           filtrar();
           function filtrar(){
               $.ajax({
                   type: "POST",
                   data: ordenar, 
                   url: "../controllers/get_temas.php?id="+<?=$idBloque;?>,
                   success: function(msg){
                       //alert(msg);
                       var msg = jQuery.parseJSON(msg);
                       if(msg.error == 0){
                           //alert(msg.dataRes[0].id);
                           $("#data tbody").html("");
                           $.each(msg.dataRes, function(i, item){
                               var newRow = '<tr>'
                                    +'<td>'+msg.dataRes[i].id+'</td>'   
                                    +'<td>'+msg.dataRes[i].nombre+'</td>'   
                                    +'<td>'+msg.dataRes[i].creado+'</td>' 
                                    +'<td><a href="admin_add_banco_subtemas.php?idNivel='+<?=$idNivel;?>+'&idGrado='+<?=$idGrado;?>+'&idMateria='+<?=$idMateria;?>+'&idBloque='+<?=$idBloque;?>+'&idTema='+msg.dataRes[i].id+'" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></a></td>'
                                    +'</tr>';
                                $(newRow).appendTo("#data tbody");
                           });
                       }else{
                           var newRow = '<tr><td></td><td>'+msg.msgErr+'</td></tr>';
                           $("#data tbody").html(newRow);
                       }
                   }
               });
           }
           
           //Ordenar ASC y DESC header tabla
            $("#data th span").click(function(){
                if($(this).hasClass("desc")){
                    $("#data th span").removeClass("desc").removeClass("asc");
                    $(this).addClass("asc");
                    ordenar = "&orderby="+$(this).attr("title")+" asc";
                }else{
                    $("#data th span").removeClass("desc").removeClass("asc");
                    $(this).addClass("desc");
                    ordenar = "&orderby="+$(this).attr("title")+" desc";
                }
                filtrar();
            });
           
           //añadir nuevo
           $('#formAdd').validate({
                rules: {
                    inputName: {required: true}
                },
                messages: {
                    inputName: "Nombre del tema obligatorio"
                },
                tooltip_options: {
                    inputName: {trigger: "focus", placement: "bottom"}
                },
                submitHandler: function(form){
                    $.ajax({
                        type: "POST",
                        url: "../controllers/admin_add_banco_tema.php",
                        data: $('form#formAdd').serialize(),
                        success: function(msg){
                            var msg = jQuery.parseJSON(msg);
                            if(msg.error == 0){
                                $('.msgModal').css({color: "#77DD77"});
                                $('.msgModal').html(msg.msgErr);
                                setTimeout(function () {
                                  location.href = 'admin_add_banco_temas.php?idNivel='+<?=$idNivel;?>+'&idGrado='+<?=$idGrado;?>+'&idMateria='+<?= $idMateria; ?>+'&idBloque='+<?= $idBloque; ?>;
                                }, 1500);
                            }else{
                                $('.msgModal').css({color: "#FF0000"});
                                $('.msgModal').html(msg.msgErr);
                            }
                        }, error: function(){
                            alert("Error al crear nuevo tema");
                        }
                    });
                }
            }); // end añadir nueva materia
           
        });
    </script>
    
<?php
    }//end if-else
    include ('footer.php');
?>

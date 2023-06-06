<?php

require  'db/config.php';





if(isset($_POST['save']) && !empty($_POST['name'])){
  $poisk = $_POST['name'];

  $sth = $pdo->prepare('INSERT INTO todo_list (name) VALUES(?)');
  $sth->execute([$poisk]); 

  
}


if(isset($_POST['delete'])){
  print_r($_POST);
  $del = $_POST['id'];

  $sth = $pdo->prepare('DELETE FROM todo_list WHERE id = ?'); 
  $sth->execute([$del]); 
  // всё сработало! https://i.gifer.com/Fgu.mp4 гифка успеха ура что это г

}


if(isset($_POST['finish'])){  
   $fine = $_POST['id'];
   $status = $_POST['status'] == 1 ? 0 : 1; 
    // это супер короткое if else где если статус равен 1 то он будет перезаписываться как 0 а если статус равен 0 то он перезапишется как 1 
    // if ($_POST['status'] == 1) {
    //   $status = 0;
    // } else {
    //   $status = 1;
    // }
    // это тоже самое что и свехру 



   $sth = $pdo->prepare('UPDATE todo_list SET status = ? WHERE id = ?');
   $sth->execute([$status,$fine]);

}




// $arr = [1, 2 ,3 ,4 ,5 ,6 ,7 ,8];

// // заводим счетчик
// $num = 0;

// foreach($arr as $item) {
//   $num += 1;
// }

// echo $num;

// echo '<br></br>';

// // всё

// $arr2 = [3,4,5,6,10];
// // получилось их же 4 

// foreach ($arr2 as $key => $value) {
//   $key+= 1;
// }

// echo $key;




if(isset($_POST['sav'])){
  $id = $_POST['id'];
  $update = $_POST['name'];
    

  $sth = $pdo->prepare('UPDATE todo_list SET name = ? WHERE id = ?');
    $sth->execute([$update,$id]);





}


if(isset($_POST['search'])  && !empty($_POST['name'])){

  $query = $_POST['name'];

  $name = "%$query%";
  // придётся так как LIKE без этого %% Не будет расботать 

  $sth = $pdo->prepare('SELECT * FROM todo_list WHERE name LIKE ? ORDER BY id DESC');
  $sth->execute([$name]);
  $data = $sth->fetchAll();
  // делаем поиск 


} else{
  $data = $pdo->query('SELECT * FROM todo_list ORDER BY id DESC')->fetchAll();
}


// $data = $pdo->query('SELECT * FROM todo_list ORDER BY id DESC')->fetchAll();
// массив data это все записи в таблице todo_list
// ORDER BY id DESC делаем так что бы записи добавлялиьс по убыванию 
// то есть то что мы добавили будет первым в списке 


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo list</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>

<style>

tr{
  vertical-align: middle;
}


.finished{
  background-color: lightgreen;
}

.name-finish {
    text-decoration: line-through;
  }

</style>

<body>

<section class="vh-100" style="background-color: #eee;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col col-lg-9 col-xl-7">
        <div class="card rounded-3">
          <div class="card-body p-4">

            <h4 class="text-center my-3 pb-3">To Do App</h4>

            <form method="POST" action="" class="row row-cols-lg-auto g-3 justify-content-center align-items-center mb-4 pb-2">
              <div class="col-12">
                <div class="form-outline">
                  <input type="text" name="name" id="name" class="form-control" />
                  <!-- когда мы инпуту даём name то он превращается в ключик в массиве POST -->
                </div>
              </div>

              <div class="col-12">
                <button type="submit" name="save" class="btn btn-primary">Save</button>
              </div>

              <div class="col-12">
                <button type="submit" name="search" class="btn btn-warning">Get tasks</button>
              </div>
            </form>

            <table class="table mb-4">
              <thead>
                <tr>
                  <th  scope="col">No.</th>
                  <th scope="col">Todo item</th>
                  <th scope="col">Status</th>
                  <th scope="col">Actions</th>
                </tr>
              </thead>
              <tbody>

              <?php if(!empty($data)): ?>
                <?php foreach($data as $key => $value): ?>
                <!-- каждая запись в массиве data это переменая $value -->
                  
                <tr <?=$value['status'] == 0 ? 'class="finished"' : '' ?>>
                <!-- если status finished то есть 0 то вешаем класс "finished"  -->
                  <th scope="row"><?= $key + 1 ?> </th>
                  <!-- к key мы прибавляем один потому что сам key начинается с 0 так как это массив -->
                  <!-- а нам нужно что бы он начинался с 1 поэтому мы добавили +1 это добавляет эелемнет -->
                  <!-- то есть цифра один будет под ключём 0 а дальше начнутся записис с 1 -->
                  <?php //echo $value ?>
                  <td <?=$value['status'] == 0 ? 'class="name-finish"' : '' ?>> <?= htmlspecialchars($value['name']) ?></td>
                  <!-- htmlspecialchars это встроеная функция которая декодирует html код вставленый в инпут -->
                  <!-- если допусим в иинпут хакер хочет вставить html код чтобы взломать  -->
                  <td><?=$value['status'] == 1 ? 'in progress' : 'finish' ?></td>
                  <!-- Чтобы php скрипт понял, какую запись мы хотим удалить,
                    нам нужно её отправлять вместе с формой -->
                    
                  <td>
                    <form method="POST"> 
                      <input type="hidden" name="id" value="<?= $value['id'] ?>">
                      <input type="hidden" name="status" value="<?= $value['status'] ?>">
                      <button name="delete" class="btn btn-danger">Delete</button>
                     
                      <button class="btn btn-success ms-1 " name="finish">
                        
                      <?= $value['status'] == 0 ? 'No finished' : 'Finshed' ?>
                      <!-- если status равен 0 то тогда название кнопки будет меняться на No finished а если не равно 0 тогда кнопка будет называться finished  -->
                    
                      <?php if($value['status'] == 1 ):  ?>
                     <!-- ___________________________________________________ -->
                          
                      <button type="button" class="btn btn-warning ms-1" name="edit" data-toggle="modal" data-target="#exampleModal-<?=$value['id']?>">Edit</button>


                          <!-- Modal -->
                          <div class="modal fade" id="exampleModal-<?=$value['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                          <div class="modal-content">
                          <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                          </button>
                          </div>
                          <div class="modal-body">
                          <input type="text" name="name" value="<?= $value['name'] ?>"></input>
                          </div>
                          <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <button name="sav" type="submit" class="btn btn-primary">Save changes</button>
                          </div>
                          </div>
                          </div>
                          </div>


                      <?php endif; ?>
                      <!-- если статус in progress то есть 1 тогда кнопка  должна показыватся  а если статус равен 0 то есть запись завершена кнопка не будет показываться -->

                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>

               
              </tbody>
            </table>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
    
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>

</body>
</html>
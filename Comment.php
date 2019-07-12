<?php

  session_start();

  require_once('php-Action\UserModalBox.php');
  require_once('php-Action\MySQLConection.php');

  // 해당 홈페이지를 나타내는 파라미터
  $URL_ID = $_GET['db'];
  // 해당 홈페이지의 서브 도메인을 가리키는 파라미터
  $PageID = $_GET['pageID'];
  // 해당 블로그 홈페이지의 Pagination이 몇 페이지를 가리키는지 나타내는 파라미터 (디폴트 값은 항상 1)
  $PaginationID = $_GET['paginationID'];
  // 몇 개의 댓글을 기준으로 Pagination 할 것인지를 나타내는 int 값. 나중에 get 방식으로 받아오게 따로 빼서 확장해도 괜찮을 거 같다.
  $PaginationDivision = 10;

  $connectedUserProfileFileName = '';

  if(isset($_SESSION['user_id'])){
    $connectedUserID = $_SESSION['user_id'];
    $connectedUserProfileFileName = $_SESSION['profileImageFileName'];
  }

  $connect_object = MySQLConnection::DB_Connect($URL_ID);

  $fetchAllComments = "
    SELECT * FROM `" . $PageID . "`";

  class Comment{

    static public function WarnNoCommentsToShow(){
      return sprintf('
        <div class="alert alert-secondary alert-dismissible fade show">
          <button type="button" class="close" aria-label="Close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
          </button>
          <p id="NoCommentWarning" class="lead" style="font-size: 14px; color: #4c4c4c;">등록된 댓글이 없습니다.</p>
        </div>
      ');
    }

    static public function CreateComment($CommentUserId, $Content, $DateTime, $ProfileImageFileName, $CommentIndex){

      global $connectedUserID;
      global $connectedUserProfileFileName;

      #############################################################
      #                                                           #
      #  프로필 이미지 지정해 놓은 게 없는 경우, 디폴트 이미지를 표시  #
      #                                                           #
      #############################################################

      if(empty($ProfileImageFileName)){
        $ProfileImageFileName = 'img/userDefaultProfile.svg';
      }
      else{
        $ProfileImageFileName =  'profileImages/' . $ProfileImageFileName;
      }

      $profileImageElement = sprintf(
        '<img class="comment-avatar col-1.5" width="48px" height="48px" class="img-fluid rounded-circle" src="%s" alt="Image For User Profile">',
        $ProfileImageFileName
      );

      // 본인이 단 댓글인 경우, Edit, Delete Button을 활성화 함
      if(isset($connectedUserID) && $connectedUserID == $CommentUserId){
        $ElementsOnMyComment =
        '
        <span><img src="./img/trash-2.svg" width="16px" height="16px" onclick="deleteComment($(this).closest(\'li\').attr(\'id\'))"></span>
        <span><img src="./img/edit.svg" width="16px" height="16px" onclick="editComment($(this).parent().prevAll(\'p\').attr(\'id\'), $(this).parent().next())"></span>
        <span style="display: none;" class="sendCommentUpdateButton"><img src="./img/send.svg" width="16px" height="16px" onclick="sendCommentUpdateMessage($(this).parent().prevAll(\'p\').attr(\'id\'))"></span>
        ';
      }
      else {
        $ElementsOnMyComment = "";
      }

      return sprintf(
      '
        <li id="ev-comment-%s" class="row comment">
          %s
          <div class="comment col-10">
            <span class="comment-userID">%s</span>
            <span style="color: #777777; font-size: 12px;">&nbsp;&nbsp;&nbsp;%s</span>
            <br>
            <p id="comment-content-%s" class="comment-content">%s</p>
            %s
          </div>
        </li>
        <hr>
        ',
          $CommentIndex,
          $profileImageElement,
          $CommentUserId,
          $DateTime,
          $CommentIndex,
          $Content,
          $ElementsOnMyComment
        );
      }
   }


   // 로그인 되어 있다면 (쿠키가 존재하면), 해당하는 ID의 프로필 사진을 찾아 띄우고
   // 로그인 되어 있지 않다면 프로필 사진 대신 로그인 버튼을 띄운다.
   if(!isset($connectedUserID)){
     $LoginButton = '<li id="EV-Login" style="float:right;" class="nav-tab" data-toggle="modal" data-target="#LogInModal">Login</li>';
   }
   else{

     $connect_userdb = MySQLConnection::DB_Connect("userdb");

     $fetchMyProfileImage = "
       SELECT * FROM usersinfotbl WHERE ID = '" . $connectedUserID . "'";

     $ret = mysqli_query($connect_userdb, $fetchMyProfileImage);

     $row = mysqli_fetch_array($ret);

     $myProfileImageName = $row['ProfileImageFileName'];

     if(empty($myProfileImageName)){
       $myProfileImageElement = '
       <li id="EV-Logout" style="float:right;" onclick="location.href=\'./php-Action/CommentPageLogout.php\'">Logout</li>
       <li style="float:right;"><img id="connectedUser-Avatar" class="comment-avatar" data-toggle="modal" data-target="#UserInfoModal" width="25px" height="25px" class="img-fluid rounded-circle" src="img/userDefaultProfile.svg" alt="Image For User Profile"></li>';
     }
     else{
      $myProfileImageElement ='
      <li id="EV-Logout" style="float:right;" onclick="location.href=\'./php-Action/CommentPageLogout.php\'">Logout</li>
      <li style="float:right;"><img id="connectedUser-Avatar" class="comment-avatar" data-toggle="modal" data-target="#UserInfoModal" width="25px" height="25px" class="img-fluid rounded-circle" src="profileImages/'. $myProfileImageName .'" alt="Image For User Profile"></li>';
     }
   }

?>

<!DOCTYPE html>
<html lang="kr" dir="ltr">
  <head>
    <title>EV Comments</title>
    <meta charset="utf-8">
    <!-- 반응형 웹페이지를 위한 viewport 설정 -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- Bootstrap 스타일 시트를 적용. min이 붙은 것은 난독화 파일이기 때문.-->
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/comment.css">
    <link rel="stylesheet" href="./css/EV-Style.css">
  </head>
  <body>
    <div id="EV-Container" class="container">
      <!-- 현재 댓글의 갯수, 로그인 되어 있는 ID를 나타내는 NavBar -->
      <header id="EV-nav">
        <ul>
          <li id="EV-CommentNumber" class="nav-tab">Comments</li>
          <li id="EV-UserID" class="nav-tab"></li>
          <li id="EV-Feedback" class="nav-tab"></li>
          <?php
            if(empty($myProfileImageElement)){
              echo $LoginButton;
            }
            else{
              echo $myProfileImageElement;
            }
          ?>
        </ul>
      </header>
      <!-- 댓글 창들의 모음 컨테이너 -->
      <div>
        <div id="EV-UserInputArea">
          <!-- Avatar (셋팅된 프로필 사진) -->
            <!-- 댓글 입력 창 -->
            <div class="textarea-outer col-sm-12">
              <span id="Textarea-placeholder" onclick="textAreaClicked()">여기에 텍스트를 입력하세요..</span>
              <div id="CommentArea" class="alignLeft" width="100%" tabindex="0" role="textbox" aria-multiline="true" contenteditable="PLAINTEXT-ONLY" data-role="editable" class="text-right" title="Join the discussion..."></div>
            </div>
          <!-- 텍스트 에디터 내에 해당 태그를 붙여주는 버튼들이다. -->
          <div id="EV-Buttons">
            <ul>
              <li id="EV-Buttons-B" onclick="editButtonClicked(this.id)"><b>B</b></li>
              <li id="EV-Buttons-I" onclick="editButtonClicked(this.id)"><i>I</i></li>
              <li id="EV-Buttons-U" onclick="editButtonClicked(this.id)"><u>U</u></li>
              <li id="EV-Buttons-S" onclick="editButtonClicked(this.id)"><s>S</s></li>
              <li id="EV-Buttons-CommentSubmit" onclick="editButtonClicked(this.id)" style="float: right;">제출</li>
            </ul>
          </div>
          <div id="recommendLoginAlert" class="alert alert-success alert-dismissible fade show" style="display: none;">
            <p class="lead" style="font-size: 14px; color: #4c4c4c;">Ev Comment 서비스에 로그인하시겠습니까?<br>익명으로 댓글을 남기시려면 제출을 한 번 더 클릭해주세요.</p>
          </div>
        </div>
        <hr>
        <!-- 댓글 -->
        <div id="EV-comment">
          <ul>
            <?php

              $ret = mysqli_query($connect_object, $fetchAllComments);

              $commentsNumber = mysqli_num_rows($ret);

              // 몇 페이지가 끝인 지 계산
              if($commentsNumber % $PaginationDivision == 0){
                $PaginationEnd = $commentsNumber / $PaginationDivision;
              }
              else {
                $PaginationEnd = ($commentsNumber / $PaginationDivision) + 1;
              }

              // 댓글이 없는 경우 처리
              if($commentsNumber < 1){
                echo Comment::WarnNoCommentsToShow();
              }

              // $PaginationID에 따른 포인터 ($row) 이동
              for($i = 0; $i < $PaginationID * $PaginationDivision; $i++){
                if($i >= $commentsNumber){
                  break;
                }
                $row = mysqli_fetch_array($ret);
              }

              // $PaginationDivision만큼 댓글을 출력. 댓글이 더 없다면 break.
              for($i = 0; $i < $PaginationDivision; $i++){

                if($i >= $commentsNumber){
                  break;
                }

                $row = mysqli_fetch_array($ret);

                echo Comment::CreateComment(
                  $row['CommentUserId'],
                  $row['Content'],
                  $row['DateTime'],
                  $row['ProfileImageFileName'],
                  $row['CommentIndex']
                );
              }
            ?>
          </ul>
        </div>
      </div>

      <!-- fade 클래스를 이용해 애니메이션을 줌 -->
      <!-- tabindex에 대해선 오른쪽 참고 https://developers.google.com/web/fundamentals/accessibility/focus/using-tabindex?hl=ko -->
      <div id="UserInfoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <!-- modal-sm, modal-md, modal-lg는 modal 창 크기에 대한 부트스트랩 속성임 -->
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <?php
              if(!isset($connectedUserProfileFileName)){
                echo UserModalBox::GenerateUserInfoModal($connectedUserID, '');
              }
              else{
                echo UserModalBox::GenerateUserInfoModal($connectedUserID, $connectedUserProfileFileName);
              }
            ?>
          </div>
        </div>
      </div>

      <div id="LogInModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">EV Comment Service 로그인</h5>
              <!-- data-dismiss 속성을 통해, 취소 버튼을 누르면 모달 박스가 없어지는 것을 구현 -->
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <!-- times를 x 버튼 대신 이용함 -->
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="php-Action/CommentPageLogin.php" method="post" accept-charset="utf-8">
                <div class="form-group">
                  <label for="ID">ID</label>
                  <input id="ID" name="ID" type="text" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="PW">PW</label>
                  <input id="PW" name="PW" type="password" class="form-control" required>
                </div>
                <div class="modal-footer">
                  <!-- data-dismiss 속성을 통해, 취소 버튼을 누르면 모달 박스가 없어지는 것을 구현 -->
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">취소</button>
                  <button type="submit" class="btn btn-primary">로그인</button>
                </div>
            </div>
          </div>
        </div>
      </div>
      <div id="EV-Pagination">
        <a href="#">&laquo;</a>
        <a href="#">1</a>

        <?php
          // $PaginationDivision (페이지 나누는 기준)
          // $PaginationID (현재 페이지)
          // $PaginationEnd (끝 페이지)

          // 현재 페이지가 앞 쪽에 치우친 경우
          if($PaginationID - ($PaginationDivision / 2) <= 0){

          }

          // 현재 페이지가 뒤 쪽에 치우친 경우
          else if($PaginationEnd - $PaginationID < $PaginationDivision / 2){

          }
          // 페이지를 중앙에 놓으면 되는 경우
          else {

          }

          // 페이지네이션 할 수 있는 숫자를 몇 개까지 표시할 것인지 나타내는 int형 변수
          // (값을 바꿔도 되지만, 웹페이지 디자인 상 홀수여야 균형이 맞아보일 것 같으니 주의)
          $paginatorsNumber = 9;

          for($i = 0; $i < $paginatorsNumber; $i++){
            echo sprintf('
              <a href="https://evcommentservice.ga/Comment.php?db=%s&pageID=%s&mode=%s&paginationID=%s">%s</a>
            ', $URL_ID, $PageIdentifier, $EmotionalAnalysisMode, $paginationID, $i);
          }
        ?>

        <a href="#">&raquo;</a>
      </div>
      <footer id="EV-Footer">
        <p style="padding-top: 7px;">&copy; 2019 Team EV</p>
      </footer>
    </div>
  </body>

  <!-- 제이쿼리 자바스크립트 추가하기 -->
  <script src="./lib/jquery-3.2.1.min.js"></script>
  <!-- Popper 자바스크립트 추가하기 -->
  <script src="./lib/popper.min.js"></script>
  <!-- 부트스트랩 자바스크립트 추가하기 -->
  <script src="./lib/bootstrap.min.js"></script>
  <!-- MDB 라이브러리 추가하기 -->
  <script src="./lib/mdb.min.js"></script>
  <!-- 커스텀 자바스크립트 추가하기 -->
  <script src="./js/comment.js"></script>

</html>

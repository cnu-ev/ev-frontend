#--------------------------------------------------------
#
# This file is created by comment helper 2019-08-10
#
#--------------------------------------------------------

# Setting
setting.descNumbering        =   0
setting.issueNumbering       =   0
setting.recursiveTraversal   =   1
setting.divBySeparator   =   1
setting.divByStartEndTag   =   1

# Flags
flag.Author   =   1
flag.Last_Edited   =   0
flag.Desc   =   1
flag.Issue   =   1
flag.Sup_Div_Line   =   1
flag.Sub_Div_Line   =   1
flag.Email   =   0
flag.Telephone   =   0
flag.Github_Account   =   0
flag.Ref_URLs   =   1
flag.Created_Date   =   1
flag.Team   =   1
flag.Memo   =   0

# Globals
global.Extension        =  php, html
global.Project_Path     =  C:\xampp\ev-commentService
global.Author           =  jopemachine
global.Separator        =  
global.StartTag         =  <!--
global.EndTag           =  -->
global.Sub_Div_Line     =  ==============================+===============================================================
global.Sup_Div_Line     =  ==============================+===============================================================
global.Email            =   
global.Telephone        =   
global.Github_Account   =   
global.Team             =  team ⓒ EV for BottomUp
global.Memo             =   

# Desc
AddComment.php::desc       +=       댓글을 제출할 때 Ajax로 호출되어, 감정분석 값을 포함한 댓글 데이터를 DB에 저장합니다.
CommentPageLogin.php::desc       +=       Comment.php에서 로그인할 때 ajax로 호출합니다. evCommentService.ga에서 로그인 할 땐 SignInAction.php를 사용합니다.
CommentPageLogout.php::desc       +=       Comment.php에서 로그아웃 할 때 사용합니다. 세션을 삭제하는 식으로 작동합니다.
DeleteComment.php::desc       +=       댓글을 삭제하는 버튼을 클릭했을 때, 실행됩니다. 인자로 넘어온 UserID 값이 지우려 하는 댓글의 ID와 같다면 DB에서 댓글을 지웁니다.
DeleteService.php::desc       +=       evCommentService.ga에서 서비스를 삭제하는 버튼을 눌렀을 때 실행됩니다.
EditComment.php::desc       +=       댓글을 수정하는 버튼을 눌렀을 때 실행됩니다. 
ID_DuplicateCheck.php::desc       +=       회원가입 페이지에서 댓글 중복 검사 버튼을 눌렀을 때 실행됩니다.
MySQLConection.php::desc       +=       MySQL에 접속하기 위한 클래스인 MySQLConnection이 정의되어 있습니다. 다른 php 파일들에서 require해 사용합니다.
PopularPosting.php::desc       +=       evCommentService.ga에서 블로그에서 '인기 포스팅 분석' 서비스를 제공합니다.
Positiveness.php::desc       +=       evCommentService.ga에서 블로그에서 '좋은 평가를 받은 게시물' 서비스를 제공합니다.
PostsSortingByCommentsNumber.php::desc       +=       evCommentService.ga에서 블로그에서 '댓글이 많이 달린 게시물' 서비스를 제공합니다.
RecentComments.php::desc       +=       evCommentService.ga에서 블로그에서 '최근 달린 댓글' 서비스를 제공합니다.
RegisterURL.php::desc       +=       evCommentService.ga에서 서비스를 등록하려 시도할 때 실행됩니다. 
RegisterURL.php::desc       +=       요청으로 들어온 URL 값을 해싱해 DB를 생성하고, 특수한 두 개의 테이블인 pageTitlePairs, VisitorCounter 을 만듭니다.
ShowHomePage.php::desc       +=       evCommentService.ga에서 등록된 홈페이지를 나타내는 함수들을 갖고 있는 php 파일입니다.
SignInAction.php::desc       +=       evCommentService.ga에서 로그인할 때 실행됩니다.
SignOutAction.php::desc       +=       evCommentService.ga에서 로그아웃 할 때 실행됩니다.
SignUpAction.php::desc       +=       evCommentService.ga에 회원가입할 때 실행됩니다.
URLVerification.php::desc       +=       클라이언트 페이지에서 댓글 페이지를 요청할 때 실행됩니다.
UserEditAction.php::desc       +=       evCommentService.ga에서 회원정보를 변경할 때 실행됩니다.
UserModalBox.php::desc       +=       유저의 정보를 보여주는 ModalBox를 나타내는 함수가 정의되어 있습니다. Comment.php와 evCommentService.ga 홈페이지에서 모두 이용합니다.
CommentManageService.php::desc       +=       evCommentService.ga에서 블로그에 대한 간단한 통계 서비스를 제공합니다.
SignIn.php::desc       +=       evCommentService.ga의 로그인 페이지입니다.
URL-Register.php::desc       +=       evCommentService.ga의 서비스 등록 및 관리 페이지입니다.
UserEdit.php::desc       +=       evCommentService.ga의 회원 정보 수정 페이지입니다.
index.html::desc       +=       evCommentService.ga의 index.html의 기본 동작은 로그인 페이지로 이동하는 것입니다.
SignUp.html::desc       +=       evCommentService.ga의 회원 가입 페이지 입니다.
CommentExample.html::desc       +=       댓글 창 스타일을 확인하기 위한 예제 파일입니다.
Jekyll-Script.html::desc       +=       클라이언트가 서비스를 이용하기 위해 사용해야 하는 스크립트 파일입니다.

# Issue

# Reference URLs

# Excluded files

<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

use app\models\Posts;
use Mpdf\Mpdf;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }


    public function actionPost()
    {
        $posts = Posts::find()->all();
        //echo '<pre>'; print_r($posts); die();

        return $this->render('post', ['posts' =>$posts]);
    }


    public function actionCreate()
    {
        //echo "Create Post";
        $posts = new Posts();
        $formData = Yii::$app->request->post();
        if($posts->load($formData)){
            if($posts->save()){
                Yii::$app->getSession()->setFlash('message', 'Posts Published Successfully');
                return $this->redirect(['index']);
            }else{
                Yii::$app->getSession()->setFlash('message', 'Posts Not Published');
            }
        } 
        return $this->render('create', ['posts' => $posts]);

    }

    public function actionView($id){
        //echo "View page"; die();
        $posts = Posts::findOne($id);
        return $this->render('view', ['posts' => $posts]);
    }

    public function actionUpdate($id){
        //echo "edit page";
        $posts = Posts::findOne($id);
        if($posts->load(Yii::$app->request->post()) && $posts->save() ){
            Yii::$app->getSession()->setFlash('message', 'Posts Updated Successfully');
            return $this->redirect(['index', 'id'=> $posts->id]);
        }else{
            return $this->render('edit', ['posts' => $posts]);
        }
    }

    public function actionDelete($id){
        //echo $id;
        $posts = Posts::findOne($id)->delete();
        if($posts){
            Yii::$app->getSession()->setFlash('message', 'Post Delete Successfully');
            return $this->redirect(['index']);
        }
    }

    //V pdf generate by id
    public function actionGenPdf($id)
    {
        $posts = Posts::findOne($id);
        if ($posts === null) {
            throw new NotFoundHttpException('The requested post does not exist.');
        }
        $pdf_content = $this->renderPartial('view-pdf', ['posts' => $posts]);
        $mpdf = new mPDF();
        $mpdf->WriteHTML($pdf_content);
        $mpdf->Output();
        exit;
    }

    // V same page pdf generate
    public function actionGenMypdf()
    {
        // echo "hello"; die();
        // Render the view into $pdf_content
        $pdf_content = $this->render('my-pdf');
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($pdf_content);
        $mpdf->Output();
        exit;
    }

    
    //V pdf generate by page contents jitana dikhana chaho
    public function actionGenPdfcontent()
    {
        //echo "Hello pdf"; die();
        $posts = Posts::find()->all();
        if (empty($posts)) {
            throw new NotFoundHttpException('No posts found.');
        }
        $html = $this->renderPartial('pdf-contents', ['posts' => $posts]);
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);

        $mpdf->Output('filename.pdf', 'I'); // 'D' for download, 'I' for inline, 'F' for save to file

        Yii::$app->end();
    }


    // V Chart Page Opening
    public function actionChart()
    {
        return $this->render('chart');
    }



    // V same full page pdf chart
    // public function actionChartpdf()
    // {
    //     // echo "hello"; die();
    //     $pdf_content = $this->render('chart');
    //     $mpdf = new \Mpdf\Mpdf();
    //     $mpdf->WriteHTML($pdf_content);
    //     $mpdf->Output();
    //     exit;
    // }


    // 1. Pahle image banakar chart ko save karaya pdf me liye
    public function actionSaveChart()
    {
        if (Yii::$app->request->isPost) {
            $imageData = Yii::$app->request->post('image');

            // Decode the image data
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($imageData);

            // Set file path
            $filePath = Yii::getAlias('@webroot/images/chart.png');
            echo 'File Path: ' . $filePath; // Debugging file path

            // Save the image to a file
            if (file_put_contents($filePath, $imageData)) {
                return 'Image saved successfully.';
            } else {
                return 'Failed to save image.';
            }
        }

        return 'Invalid request.';
    }



    // 2. phir pdf chart create karwaya
    // public function actionChartpdf()
    // {
    //     $filePath = Yii::getAlias('@webroot/images/chart.png');
    //     $pdfContent = '<h1>Chart</h1>';
    //     if (file_exists($filePath)) {
    //         $pdfContent .= '<img src="' . Yii::getAlias('@web') . '/images/chart.png" alt="Chart">';
    //     } else {
    //         $pdfContent .= '<p>No chart image found.</p>';
    //     }

    //     $mpdf = new \Mpdf\Mpdf();
    //     $mpdf->WriteHTML($pdfContent);
    //     $mpdf->Output();
    //     exit;
    // }


    public function actionChartpdf()
    {
        $chartFilePath = Yii::getAlias('@webroot/images/chart.png');
        $npsFilePath = Yii::getAlias('@webroot/images/nps.jpeg');
        $pdfContent = '<h1>Chart</h1>';

        // Check if chart image exists
        if (file_exists($chartFilePath)) {
            $absoluteChartFilePath = Yii::getAlias('@web') . '/images/chart.png';
            $pdfContent .= '<img src="' . $absoluteChartFilePath . '" alt="Chart" style="width: 100%; height: auto;">';
        } else {
            $pdfContent .= '<p>No chart image found.</p>';
        }

        // Check if NPS image exists
        if (file_exists($npsFilePath)) {
            $absoluteNpsFilePath = Yii::getAlias('@web') . '/images/nps.jpeg';
            $pdfContent .= '<h2>NPS Image</h2>';
            $pdfContent .= '<img src="' . $absoluteNpsFilePath . '" alt="NPS Image" style="width: 100%; height: auto;">';
        } else {
            $pdfContent .= '<p>No NPS image found.</p>';
        }

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($pdfContent);
        $mpdf->Output();
        exit;
    }




    // 1 V Chart Page Opening
    public function actionHighchart()
    {
        return $this->render('highchart');
    }



    // 2. Pahle image banakar chart ko save karaya pdf me liye
    
    public function actionSaveHighchart()
    {
        if (Yii::$app->request->isPost) {
            $imageData = Yii::$app->request->post('image');

            // Decode the image data
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($imageData);

            // Set file path
            $filePath = Yii::getAlias('@webroot/images/highchart.png');
            echo 'File Path: ' . $filePath; // Debugging file path

            // Save the image to a file
            if (file_put_contents($filePath, $imageData)) {
                return 'Image saved successfully.';
            } else {
                return 'Failed to save image.';
            }
        }

        return 'Invalid request.';
    }





    public function actionHighchartpdf()
    {
        $chartFilePath = Yii::getAlias('@webroot/images/highchart.png');
        // $npsFilePath = Yii::getAlias('@webroot/images/nps.jpeg');
        $pdfContent = '<h1>Chart</h1>';

        // Check if chart image exists
        if (file_exists($chartFilePath)) {
            $absoluteChartFilePath = Yii::getAlias('@web') . '/images/highchart.png';
            $pdfContent .= '<img src="' . $absoluteChartFilePath . '" alt="Chart" style="width: 100%; height: auto;">';
        } else {
            $pdfContent .= '<p>No chart image found.</p>';
        }

        

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($pdfContent);
        $mpdf->Output();
        exit;
    }






    // 1 V Canvas Chart Page Opening START
    public function actionChartcanvas()
    {
        return $this->render('canvaschart');
    }


    // 2. Pahle canvas image banakar chart ko save karaya pdf me liye
    public function actionSaveCanvaschart()
    {
        if (Yii::$app->request->isPost) {
            $imageData = Yii::$app->request->post('image');

            // Decode the image data
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($imageData);

            // Set file path
            $filePath = Yii::getAlias('@webroot/images/canvaschart.png');
            echo 'File Path: ' . $filePath; // Debugging file path

            // Save the image to a file
            if (file_put_contents($filePath, $imageData)) {
                return 'Image saved successfully.';
            } else {
                return 'Failed to save image.';
            }
        }

        return 'Invalid request.';
    }


    // 3 Canvas Pdf Generate with image
    public function actionCanvaschartpdf()
    {
        $chartFilePath = Yii::getAlias('@webroot/images/canvaschart.png');
        // $npsFilePath = Yii::getAlias('@webroot/images/nps.jpeg');
        $pdfContent = '<h1>Chart</h1>';

        // Check if chart image exists
        if (file_exists($chartFilePath)) {
            $absoluteChartFilePath = Yii::getAlias('@web') . '/images/canvaschart.png';
            $pdfContent .= '<img src="' . $absoluteChartFilePath . '" alt="Chart" style="width: 100%; height: auto;">';
        } else {
            $pdfContent .= '<p>No chart image found.</p>';
        }

        

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($pdfContent);
        $mpdf->Output();
        exit;
    }




}

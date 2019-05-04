<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleComments;
use App\Entity\ArticleLikes;
use App\Entity\CommentLikes;
use App\Form\ArticleType;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Form\UserType;

class ArticleController extends AbstractController
{
    const PER_PAGE = 2;

    /**
     * @Route("/create/article", name="create_article"),
     * @Method({"GET", "POST"})
     */
    public function CreateArticles(Request $request)
    {


    }

    /**
     * @Route("/articles", name="view_articles"),
     * @Method({"GET", "POST"})
     */
    public function Articles(Request $request)
    {
        $article = new Article();
        $articleForm = $this->createForm(ArticleType::class, $article);
        $articleForm->handleRequest($request);

        if($articleForm->isSubmitted() && $articleForm->isValid()) {
            try {
                $article = $articleForm->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $article->setUserId($this->getUser()->getId());
                $entityManager->persist($article);
                $entityManager->flush();

                $this->addFlash("addArticle", "Новината е добавена успешно!");

                return $this->redirectToRoute('view_articles');//refresh
            } catch (\Exception $e) {
                $this->addFlash("ErrorArticle", "Данните не бяха записани успешно!");
            }
        }

        //$articles = $this->getDoctrine()->getManager()->getRepository(Article::class)->findAll();
        $userArticles = $this->getDoctrine()->getRepository(User::class)->findUserArticles($request->get('page', 1)-1, self::PER_PAGE);

        foreach ($userArticles as $k => $userArticle) {
            $comments = $this->getDoctrine()->getRepository(ArticleComments::class)->findByArticle($userArticle['id']);
            foreach ($comments as $kk => $comment) {
                $comments[$kk]['count'] = $this->getDoctrine()->getRepository(CommentLikes::class)->findCount($comment['id']);
            }
            $userArticles[$k]['comments'] = $comments;
            $userArticles[$k]['count'] =  $this->getDoctrine()->getRepository(ArticleLikes::class)->findCount($userArticle['id']);
        }

        $articlesCount = $this->getDoctrine()->getManager()->getRepository(Article::class)->findByArticlesCount();
        $pages = ceil($articlesCount / self::PER_PAGE);

        return $this->render('articles/view_articles.html.twig', [
            'articleForm' => $articleForm->createView(),
            'userArticles' => $userArticles,
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/my-articles", name="my_articles"),
     * @Method({"GET", "POST"})
     */
    public function myArticles(Request $request)
    {
        $articlesByUsername = $this->getDoctrine()->getRepository(Article::class)
                ->findArticleByUsername($this->getUser()->getUsername(), $request->get('page', 1)-1, self::PER_PAGE);

        foreach ($articlesByUsername as $k => $userArticle) {
            $comments = $this->getDoctrine()->getRepository(ArticleComments::class)->findByArticle($userArticle['id']);
            foreach ($comments as $kk => $comment) {
                $comments[$kk]['count'] = $this->getDoctrine()->getRepository(CommentLikes::class)->findCount($comment['id']);
            }
            $articlesByUsername[$k]['comments'] = $comments;
            $articlesByUsername[$k]['count'] =  $this->getDoctrine()->getRepository(ArticleLikes::class)->findCount($userArticle['id']);
        }

        $articlesCountFromCurrentUser = $this->getDoctrine()->getManager()->getRepository(Article::class)
            ->findByArticlesCountFromCurrentUser($this->getUser()->getUsername());
        $pages = ceil($articlesCountFromCurrentUser / self::PER_PAGE);

        return $this->render('articles/view_my_articles.html.twig', [
            'articlesByUsername' => $articlesByUsername,
            'pages' => $pages
        ]);

    }

    /**
     * @Route("/edit-article/{id}", name="edit_article"),
     * @Method({"GET", "POST"})
     */
    public function editArticle(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $articleForm = $this->createForm(ArticleType::class, $article);
        $articleForm->handleRequest($request);

        if($articleForm->isSubmitted() && $articleForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash("editArticle", $article->getTitle() . " е редактирана успешно!" );

            return $this->redirectToRoute('my_articles');
        }

        return $this->render('articles/edit_article.html.twig', [
            'articleForm' => $articleForm->createView()
        ]);
    }

    /**
     * @Route("/my-articles/delete/{id}", name="delete_article"),
     * @Method({"GET", "POST"})
     */
    public function deleteArticle($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        $this->addFlash("deleteArticle", $article->getTitle() . " е изтрита!" );

        return $this->redirectToRoute('my_articles');
    }

    /**
     * @Route("/like-post", name="like_post"),
     * @Method({"GET", "POST"})
     */
    public function LikePost(Request $request)
    {
        $res = ['success' => true];

        $articleid = $request->request->get('id');
        $userId =  $this->getUser()->getId();

        $entity = new ArticleLikes();

        $hasArticleLike = $this->getDoctrine()->getRepository(ArticleLikes::class)->findArticleLike($userId, $articleid);

        if (is_null($hasArticleLike)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entity->setUserId($userId);
            $entity->setArticleId($articleid);
            $entityManager->persist($entity);
            $entityManager->flush();
        } else {
            $this->getDoctrine()->getRepository(ArticleLikes::class)->deleteLikeFromArticle($userId, $articleid);
        }

        $res['count'] =  $this->getDoctrine()->getRepository(ArticleLikes::class)->findCount($articleid);

        return $this->json($res);
    }

    /**
     * @Route("/like-comment", name="like_comment"),
     * @Method({"GET", "POST"})
     */
    public function LikeComment(Request $request)
    {
        $res = ['success' => true];

        $commentId = $request->request->get('id');
        $userId =  $this->getUser()->getId();

        $commentEntity = new CommentLikes();
        $hasCommentLike = $this->getDoctrine()->getRepository(CommentLikes::class)->findComentLike($userId, $commentId);

        if (is_null($hasCommentLike)) {
            $entityManager = $this->getDoctrine()->getManager();
            $commentEntity->setUserId($userId);
            $commentEntity->setCommentId($commentId);
            $entityManager->persist($commentEntity);
            $entityManager->flush();
        } else {
            $this->getDoctrine()->getRepository(CommentLikes::class)->deleteLikeFromComment($userId, $commentId);
        }

        $res['count'] =  $this->getDoctrine()->getRepository(CommentLikes::class)->findCount($commentId);

        return $this->json($res);
    }

    /**
     * @Route("/add-comment", name="add_comment"),
     * @Method({"GET", "POST"})
     */
    public function AddComment(Request $request)
    {
        $res = ['success' => true];

        $comment = $request->request->get('comment');
        $articleId = (int) $request->get('id');
        $userId =  $this->getUser()->getId();

        $commentEntity = new ArticleComments();

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $commentEntity->setArticleId($articleId);
            $commentEntity->setUserId($userId);
            $commentEntity->setComment($comment);
            $entityManager->persist($commentEntity);
            $entityManager->flush();
        } catch (\Throwable $e) {
            $res['success'] = false;
            $res['error'] = $e->getMessage();
        }

        $res['comment'] = $comment;
        $res['id'] = $commentEntity->getId();
        $res['article_id'] = $articleId;
        $res['username'] = $this->getUser()->getUsername();

        return $this->json($res);
    }
}
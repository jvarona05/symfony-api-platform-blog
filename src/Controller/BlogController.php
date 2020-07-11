<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/posts")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/", name="post_list")
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        $page = $request->get('pager', 1);
        $limit = $request->get('limit', 10);
        $posts = $this->getDoctrine()->getRepository(BlogPost::class)->findAll();

        return $this->json([
            'page' => $page,
            'limit' => $limit,
            'data' => array_map(function(BlogPost $post){
                return $this->generateUrl('post_by_slug', ['slug' => $post->getSlug()]);
            }, $posts)
        ]);
    }

    /**
     * @Route("/add", name="post_add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $serializer = $this->get('serializer');

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }

    /**
     * @Route("/{id}", name="post_by_id", requirements={"id"="\d+"}, methods={"GET"})
     * @param BlogPost $post
     * @return JsonResponse
     */
    public function post(BlogPost $post)
    {
        return $this->json($post);
    }

    /**
     * @Route("/{slug}", name="post_by_slug", methods={"GET"})
     * @param BlogPost $post
     * @return JsonResponse
     */
    public function postBySlug(BlogPost $post)
    {
        return $this->json($post);
    }

    /**
     * @Route("/{id}", name="post_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     * @param BlogPost $post
     * @return JsonResponse
     */
    public function delete(BlogPost $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

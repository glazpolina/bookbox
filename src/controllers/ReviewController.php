<?php
class ReviewController
{
    private $service;

    public function __construct()
    {
        $this->service = new ReviewService();
    }

    public function getByBook($bookId)
    {
        $reviews = $this->service->getByBook($bookId);
        echo json_encode($reviews);
    }

    public function create()
    {
        $token = JWT::getFromHeader();
        $user = $token ? JWT::validate($token) : null;

        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->service->create($data, $user);
        http_response_code($result['status']);
        echo json_encode($result['success'] ? ['message' => 'Review added'] : ['errors' => $result['errors']]);
    }

    public function delete($id)
    {
        $token = JWT::getFromHeader();
        $user = $token ? JWT::validate($token) : null;

        $result = $this->service->delete($id, $user);
        http_response_code($result['status']);
        if (!$result['success']) {
            echo json_encode(['errors' => $result['errors']]);
        }
    }
}

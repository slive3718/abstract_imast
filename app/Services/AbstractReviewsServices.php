<?php

namespace App\Services;

use App\Models\AbstractReviewModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Model;

class AbstractReviewsServices extends BaseService
{
    private AbstractReviewModel $reviewModel;
    private ?PapersModel $paperModel = null;
    private ?PaperAuthorsModel $authorModel = null;

    public function __construct(
        ?AbstractReviewModel $reviewModel = null,
        ?PapersModel $paperModel = null,
        ?PaperAuthorsModel $authorModel = null
    ) {
        $this->reviewModel  = $reviewModel  ?? model(AbstractReviewModel::class);
        $this->paperModel   = $paperModel   ?? model(PapersModel::class);
        $this->authorModel  = $authorModel  ?? model(PaperAuthorsModel::class);
    }

    /**
     * Returns all reviews mapped by abstract_id → array of reviews
     *
     * @return array<int, array<string, mixed>>  [abstract_id => [review1, review2, ...]]
     */
    public function getReviewsMappedByPaper(): array
    {
        $reviews = $this->reviewModel
            ->findAll();

        $mapped = [];

        foreach ($reviews as $review) {
            $paperId = $review['abstract_id'];
            $mapped[$paperId][] = $review;
        }

        return $mapped;
    }

    /**
     * Get all reviews for a specific paper
     *
     * @param int $paperId
     * @param array<string, mixed> $options  e.g. ['status' => 'completed', 'limit' => 10]
     * @return array<string, mixed>[]
     */
    public function getReviewsForPaper(int $paperId, array $options = []): array
    {
        $builder = $this->reviewModel
            ->where('abstract_id', $paperId);

        // Optional filters
        if (!empty($options['status'])) {
            $builder->where('status', $options['status']);
        }

        if (!empty($options['reviewer_id'])) {
            $builder->where('reviewer_id', $options['reviewer_id']);
        }

        if (!empty($options['limit'])) {
            $builder->limit((int) $options['limit']);
        }

        return $builder
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get review counts per paper (useful for dashboard/overview)
     *
     * @return array<int, int>  [abstract_id => review_count]
     */
    public function getReviewCountsByPaper(): array
    {
        $result = $this->reviewModel
            ->select('abstract_id, COUNT(*) as review_count')
            ->groupBy('abstract_id')
            ->findAll();

        $counts = [];
        foreach ($result as $row) {
            $counts[$row['abstract_id']] = (int) $row['review_count'];
        }

        return $counts;
    }

    /**
     * Get full review data including paper title and basic author info (if needed)
     * Use this when you need more context for display/export
     *
     * @param int $paperId
     * @return array<string, mixed>
     */
    public function getDetailedReviewsForPaper(int $paperId): array
    {
        // You can extend this later with joins if needed
        $reviews = $this->getReviewsForPaper($paperId);

        // Optional: lazy load paper once if needed
        $paper = $this->paperModel->find($paperId);

        $result = [
            'paper'   => $paper,
            'reviews' => $reviews,
        ];

        return $result;
    }
}
<?php

namespace App\Services;
use App\DTO\PurchaseDTO;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\OwnershipRepositoryInterface;
class OwnerShipService
{
    private $productRepository;
    private $userRepository;
    private $ownerShipRepository;
    public function __construct(
        ProductRepositoryInterface $productRepository,
        UserRepositoryInterface $userRepository,
        OwnershipRepositoryInterface $ownershipRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->ownerShipRepository = $ownershipRepository;
    }
    public function purchase(PurchaseDTO $dto)
    {
        $user = $this->userRepository->findById($dto->user_id);
        $product = $this->productRepository->findById($dto->product_id);
        $userBalance = $this->userRepository->checkBalance($user);
        $alreadyOwned = $this->ownerShipRepository->userOwnsProduct($dto);
        if($alreadyOwned){
            //логика вывода ошибки
        }
        if($userBalance < $product->purchase_price){
            //вывод ошибки
        }




    }
}
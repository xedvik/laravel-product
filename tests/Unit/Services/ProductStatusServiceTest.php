<?php

namespace Tests\Unit\Services;

use App\DTO\Products\ProductStatusDTO;
use App\Enums\OwnershipStatus;
use App\Enums\OwnershipType;
use App\Models\OwnerShip;
use App\Models\User;
use App\Models\Product;
use App\Repositories\Interfaces\OwnershipRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\ProductStatusService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Mockery;
use Mockery\MockInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ProductStatusServiceTest extends MockeryTestCase
{
    use WithFaker;

    private $ownershipRepo;
    private $productRepo;
    private $userRepo;
    private ProductStatusService $service;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var OwnershipRepositoryInterface&MockInterface $ownershipRepo */
        $this->ownershipRepo = Mockery::mock(OwnershipRepositoryInterface::class);

        /** @var ProductRepositoryInterface&MockInterface $productRepo */
        $this->productRepo = Mockery::mock(ProductRepositoryInterface::class);

        /** @var UserRepositoryInterface&MockInterface $userRepo */
        $this->userRepo = Mockery::mock(UserRepositoryInterface::class);

        $this->service = new ProductStatusService(
            $this->ownershipRepo,
            $this->productRepo,
            $this->userRepo
        );
    }

    public function testRentedActiveDetermineProductStatus()
    {
        $ownership = [
            'type' => OwnershipType::RENT->value,
            'rental_expires_at' => now()->addHours(1)->toDateTimeString(),
        ];
        $result = $this->service->determineProductStatus($ownership);
        $this->assertEquals(OwnershipStatus::RENTED_ACTIVE, $result);
    }

    public function testRentedExpiredDetermineProductStatus()
    {
        $ownership = [
            'type' => OwnershipType::RENT->value,
            'rental_expires_at' => now()->subHours(1)->toDateTimeString(),
        ];
        $result = $this->service->determineProductStatus($ownership);
        $this->assertEquals(OwnershipStatus::RENTED_EXPIRED, $result);
    }

    public function testPurchasedDetermineProductStatus()
    {
        $ownership = [
            'type' => OwnershipType::PURCHASE->value,
        ];
        $result = $this->service->determineProductStatus($ownership);
        $this->assertEquals(OwnershipStatus::PURCHASED, $result);
    }

    public function testUnknownDetermineProductStatus()
    {
        $ownership = [
            'type' => 'unknown',
        ];
        $result = $this->service->determineProductStatus($ownership);
        $this->assertEquals(OwnershipStatus::UNKNOWN, $result);
    }

    public function testUpdateOwnershipStatus()
    {
        /** @var OwnerShip&MockInterface $ownership */
        $ownership = Mockery::mock(OwnerShip::class);

        $ownership->shouldReceive('getAttribute')->with('type')->andReturn(OwnershipType::RENT->value);
        $ownership->shouldReceive('getAttribute')->with('rental_expires_at')->andReturn(now()->addHours(1)->toDateTimeString());
        $ownership->shouldReceive('getAttribute')->with('status')->andReturn(OwnershipStatus::RENTED_ACTIVE);
        // Поскольку статус уже соответствует ожидаемому, update не должен вызываться
        $ownership->shouldNotReceive('update');
        $this->service->updateOwnershipStatus($ownership);
    }

    public function testUpdateOwnershipStatusWhenChanged()
    {
        /** @var OwnerShip&MockInterface $ownership */
        $ownership = Mockery::mock(OwnerShip::class);

        $ownership->shouldReceive('getAttribute')->with('type')->andReturn(OwnershipType::RENT->value);
        $ownership->shouldReceive('getAttribute')->with('rental_expires_at')->andReturn(now()->addHours(1)->toDateTimeString());
        $ownership->shouldReceive('getAttribute')->with('status')->andReturn(OwnershipStatus::UNKNOWN);
        // Ожидаем вызов update с новым статусом
        $ownership->shouldReceive('update')->once()->with(['status' => OwnershipStatus::RENTED_ACTIVE]);
        $this->service->updateOwnershipStatus($ownership);
    }
    public function testCheckStatusByUniqueCode()
    {
        $ownership = Mockery::mock(OwnerShip::class);
        $ownership->shouldReceive('getAttribute')->with('type')->andReturn(OwnershipType::RENT->value);
        $ownership->shouldReceive('getAttribute')->with('rental_expires_at')->andReturn(now()->addHours(1)->toDateTimeString());
        $ownership->shouldReceive('getAttribute')->with('status')->andReturn(OwnershipStatus::RENTED_ACTIVE);

        $this->ownershipRepo->shouldReceive('findByUniqueCode')->with('1234567890')->andReturn($ownership);
        $ownership->shouldReceive('update')->andReturn(true);
        $ownership->shouldReceive('fresh')->andReturn($ownership);

        $result = $this->service->checkStatusByUniqueCode('1234567890');
        $this->assertNotNull($result);
        $this->assertEquals(OwnershipStatus::RENTED_ACTIVE, $result->status);
    }

    public function testCheckUserProductStatus()
    {
        $dto = new ProductStatusDTO(
            userId: 1,
            productId: 1,
        );

        $user = Mockery::mock(User::class);
        $product = Mockery::mock(Product::class);

        $ownership = Mockery::mock(OwnerShip::class);
        $ownership->shouldReceive('getAttribute')->with('type')->andReturn(OwnershipType::RENT->value);
        $ownership->shouldReceive('getAttribute')->with('rental_expires_at')->andReturn(now()->addHours(1)->toDateTimeString());
        $ownership->shouldReceive('getAttribute')->with('status')->andReturn(OwnershipStatus::RENTED_ACTIVE);
        $ownership->shouldReceive('getAttribute')->with('unique_code')->andReturn(null, '1234567890');
        $ownership->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $ownership->shouldReceive('update')->andReturn(true);

        // Создаем новый мок для результата fresh() с уникальным кодом
        $freshOwnership = Mockery::mock(OwnerShip::class);
        $freshOwnership->shouldReceive('getAttribute')->with('unique_code')->andReturn('1234567890');
        $freshOwnership->shouldReceive('getAttribute')->with('status')->andReturn(OwnershipStatus::RENTED_ACTIVE);
        $freshOwnership->shouldReceive('getAttribute')->with('type')->andReturn(OwnershipType::RENT->value);
        $freshOwnership->shouldReceive('getAttribute')->with('rental_expires_at')->andReturn(now()->addHours(1)->toDateTimeString());
        $freshOwnership->shouldReceive('update')->andReturn(true);
        $freshOwnership->shouldReceive('fresh')->andReturn($freshOwnership);

        $ownership->shouldReceive('fresh')->andReturn($freshOwnership);

        $this->userRepo->shouldReceive('findById')->with($dto->userId)->andReturn($user);
        $this->productRepo->shouldReceive('findById')->with($dto->productId)->andReturn($product);
        $this->ownershipRepo->shouldReceive('findUserOwnership')->with(1, 1)->andReturn($ownership);
        $this->ownershipRepo->shouldReceive('generateUniqueCodeForOwnership')->with(1)->andReturn('1234567890');
        $result = $this->service->checkUserProductStatus($dto);

        $this->assertNotNull($result);
        $this->assertEquals('1234567890', $result->unique_code);
        $this->assertEquals(OwnershipStatus::RENTED_ACTIVE, $result->status);
    }
}
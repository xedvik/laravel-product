<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected ?string $context = null;
    public function __construct($resource, ?string $context = 'default')
    {
        parent::__construct($resource);
        $this->context = $context;
    }
    public function toArray($request)
    {
        return match ($this->context) {
            'default' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'balance' => $this->balance,
                'ownerships' => $this->whenLoaded('ownerships', function () {
                    return new OwnershipCollection($this->ownerships);
                }),
            ],
            'balance' => [
                'balance' => $this->balance,
            ],
            default => [],
        };
    }
}
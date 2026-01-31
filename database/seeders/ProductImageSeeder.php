<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images = [
            'city-police-station' => 'https://images.brickset.com/sets/images/60316-1.jpg',
            'city-fire-station' => 'https://images.brickset.com/sets/images/60321-1.jpg',
            'city-police-chase' => 'https://images.brickset.com/sets/images/60319-1.jpg',
            'city-express-train' => 'https://images.brickset.com/sets/images/60337-1.jpg',
            'city-construction-crane' => 'https://images.brickset.com/sets/images/60324-1.jpg',
            'city-arctic-station' => 'https://images.brickset.com/sets/images/60376-1.jpg',
            'x-wing-starfighter' => 'https://images.brickset.com/sets/images/75355-1.jpg',
            'tie-fighter' => 'https://images.brickset.com/sets/images/75347-1.jpg',
            'mandalorian-ship' => 'https://images.brickset.com/sets/images/75364-1.jpg',
            'vader-helmet' => 'https://images.brickset.com/sets/images/75304-1.jpg',
            '501-battle-pack' => 'https://images.brickset.com/sets/images/75345-1.jpg',
            'atat-walker' => 'https://images.brickset.com/sets/images/75288-1.jpg',
            'technic-bugatti' => 'https://images.brickset.com/sets/images/42083-1.jpg',
            'technic-lamborghini' => 'https://images.brickset.com/sets/images/42115-1.jpg',
            'technic-4x4' => 'https://images.brickset.com/sets/images/42122-1.jpg',
            'technic-fire-plane' => 'https://images.brickset.com/sets/images/42152-1.jpg',
            'technic-ducati' => 'https://images.brickset.com/sets/images/42107-1.jpg',
            'technic-dump-truck' => 'https://images.brickset.com/sets/images/42147-1.jpg',
            'friends-heartlake-cafe' => 'https://images.brickset.com/sets/images/41719-1.jpg',
            'friends-vet-clinic' => 'https://images.brickset.com/sets/images/41727-1.jpg',
            'friends-friendship-house' => 'https://images.brickset.com/sets/images/41340-1.jpg',
            'friends-adventure-bus' => 'https://images.brickset.com/sets/images/41734-1.jpg',
            'friends-candy-shop' => 'https://images.brickset.com/sets/images/42649-1.jpg',
            'friends-rescue-heli' => 'https://images.brickset.com/sets/images/41701-1.jpg',
            'creator-modular-house' => 'https://images.brickset.com/sets/images/31141-1.jpg',
            'creator-tiger' => 'https://images.brickset.com/sets/images/31129-1.jpg',
            'creator-space-rover' => 'https://images.brickset.com/sets/images/31107-1.jpg',
            'creator-retro-bike' => 'https://images.brickset.com/sets/images/31135-1.jpg',
            'creator-lion-tower' => 'https://images.brickset.com/sets/images/31120-1.jpg',
            'creator-modern-house' => 'https://images.brickset.com/sets/images/31139-1.jpg',
            'ninjago-justice-dragon' => 'https://images.brickset.com/sets/images/71794-1.jpg',
            'ninjago-lloyd-mech' => 'https://images.brickset.com/sets/images/71785-1.jpg',
            'ninjago-thunder-jet' => 'https://images.brickset.com/sets/images/71784-1.jpg',
            'ninjago-dragon-temple' => 'https://images.brickset.com/sets/images/71795-1.jpg',
            'ninjago-kai-spinner' => 'https://images.brickset.com/sets/images/71778-1.jpg',
            'ninjago-fire-mech' => 'https://images.brickset.com/sets/images/71783-1.jpg',
            'city-rescue-helicopter' => 'https://images.brickset.com/sets/images/60405-1.jpg',
            'city-ambulance' => 'https://images.brickset.com/sets/images/60404-1.jpg',
            'creator-cruise-ship' => 'https://images.brickset.com/sets/images/31153-1.jpg',
            'technic-heavy-crane' => 'https://images.brickset.com/sets/images/42146-1.jpg',
            'rebel-base' => 'https://images.brickset.com/sets/images/75362-1.jpg',
            'friends-pet-hotel' => 'https://images.brickset.com/sets/images/42638-1.jpg',
            'ninjago-danger-net' => 'https://images.brickset.com/sets/images/71797-1.jpg',
            'creator-space-shuttle' => 'https://images.brickset.com/sets/images/31134-1.jpg',
            'city-airport' => 'https://images.brickset.com/sets/images/60367-1.jpg',
            'armored-transport' => 'https://images.brickset.com/sets/images/75219-1.jpg',
            'technic-excavator' => 'https://images.brickset.com/sets/images/42121-1.jpg',
            'friends-beach-rescue' => 'https://images.brickset.com/sets/images/41736-1.jpg',
            'ninjago-battle-bike' => 'https://images.brickset.com/sets/images/71795-1.jpg',
            'creator-townhouse' => 'https://images.brickset.com/sets/images/31139-1.jpg',
            'city-police-heli' => 'https://images.brickset.com/sets/images/60275-1.jpg',
            'raider-shuttle' => 'https://images.brickset.com/sets/images/75354-1.jpg',
            'technic-rescue-heli' => 'https://images.brickset.com/sets/images/42145-1.jpg',
            'friends-eco-house' => 'https://images.brickset.com/sets/images/41700-1.jpg',
            'ninjago-dragon-race' => 'https://images.brickset.com/sets/images/71790-1.jpg',
            'city-hospital' => 'https://images.brickset.com/sets/images/60330-1.jpg',
            'city-police-academy' => 'https://images.brickset.com/sets/images/60372-1.jpg',
            'technic-mclaren' => 'https://images.brickset.com/sets/images/42141-1.jpg',
            'technic-volvo-loader' => 'https://images.brickset.com/sets/images/42030-1.jpg',
            'millennium-falcon' => 'https://images.brickset.com/sets/images/75257-1.jpg',
            'imperial-destroyer' => 'https://images.brickset.com/sets/images/75394-1.jpg',
            'friends-pet-shop' => 'https://images.brickset.com/sets/images/42650-1.jpg',
            'friends-school' => 'https://images.brickset.com/sets/images/41731-1.jpg',
            'creator-pickup' => 'https://images.brickset.com/sets/images/31131-1.jpg',
            'ninjago-titan-mech' => 'https://images.brickset.com/sets/images/71765-1.jpg',
        ];

        foreach (Product::all() as $product) {
            $path = $images[$product->slug] ?? null;
            if (! $path) {
                continue;
            }

            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'is_main' => true,
            ]);
        }
    }
}

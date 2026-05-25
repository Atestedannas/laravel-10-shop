<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Goods;
use App\Models\GoodsImage;
use App\Models\GoodsService;
use App\Models\GoodsSku;
use App\Models\GoodsSpec;
use App\Models\GoodsSpecValue;
use App\Models\Region;
use App\Models\Role;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 1. 角色
        // ============================================================
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => '超级管理员', 'level' => 100]
        );
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => '管理员', 'level' => 50]
        );
        $userRole = Role::firstOrCreate(
            ['slug' => 'user'],
            ['name' => '普通用户', 'level' => 0]
        );

        // ============================================================
        // 2. 用户
        // ============================================================
        // 超级管理员
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Super Admin',
                'nickname' => '超级管理员',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );
        $adminUser->roles()->sync([$superAdminRole->id]);

        // 测试用户（mobile 登录）
        $testUser = User::firstOrCreate(
            ['mobile' => '13800138000'],
            [
                'name'     => '测试用户',
                'nickname' => '小明',
                'email'    => '13800138000@shop.test',
                'password' => Hash::make('123456'),
                'point'    => 500,
                'sex'      => 1,
                'is_admin' => false,
            ]
        );
        $testUser->roles()->sync([$userRole->id]);

        // ============================================================
        // 3. 商品分类
        // ============================================================
        $cateDigital = Category::firstOrCreate(
            ['name' => '数码产品', 'parent_id' => 0],
            ['sort' => 1, 'status' => 1]
        );
        $cateClothing = Category::firstOrCreate(
            ['name' => '服装鞋帽', 'parent_id' => 0],
            ['sort' => 2, 'status' => 1]
        );
        $cateFood = Category::firstOrCreate(
            ['name' => '食品饮料', 'parent_id' => 0],
            ['sort' => 3, 'status' => 1]
        );

        // 子分类
        $catePhone = Category::firstOrCreate(
            ['name' => '手机', 'parent_id' => $cateDigital->id],
            ['sort' => 1, 'status' => 1]
        );
        $cateHeadphone = Category::firstOrCreate(
            ['name' => '耳机', 'parent_id' => $cateDigital->id],
            ['sort' => 2, 'status' => 1]
        );

        // ============================================================
        // 4. 商品
        // ============================================================

        // --- 商品1：单规格 ---
        $goods1 = Goods::firstOrCreate(
            ['goods_name' => '无线蓝牙耳机 Pro'],
            [
                'category_id'     => $cateHeadphone->id,
                'goods_image'     => '/images/goods/earphone-pro.jpg',
                'selling_point'   => '主动降噪 | 30小时续航 | Hi-Fi音质',
                'spec_type'       => 10, // 单规格
                'content'         => '<p>这是一款高品质无线蓝牙耳机，支持主动降噪，续航长达30小时。</p><p>采用最新蓝牙5.3技术，连接稳定，延迟低至45ms。</p>',
                'goods_price_min' => 299.00,
                'line_price_min'  => 499.00,
                'goods_sales'     => 1280,
                'goods_status'    => 10,
                'sort'            => 1,
            ]
        );
        // 图片
        GoodsImage::firstOrCreate(
            ['goods_id' => $goods1->id, 'image_url' => '/images/goods/earphone-pro-1.jpg'],
            ['sort' => 1]
        );
        GoodsImage::firstOrCreate(
            ['goods_id' => $goods1->id, 'image_url' => '/images/goods/earphone-pro-2.jpg'],
            ['sort' => 2]
        );
        GoodsImage::firstOrCreate(
            ['goods_id' => $goods1->id, 'image_url' => '/images/goods/earphone-pro-3.jpg'],
            ['sort' => 3]
        );
        // 单规格 SKU
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods1->id, 'sku_spec_ids' => ''],
            [
                'goods_price'  => 299.00,
                'line_price'   => 499.00,
                'stock'        => 500,
                'goods_weight' => 0.25,
                'goods_no'     => 'EP20240001',
            ]
        );
        // 服务
        GoodsService::firstOrCreate(['goods_id' => $goods1->id, 'service_name' => '7天无理由退换']);
        GoodsService::firstOrCreate(['goods_id' => $goods1->id, 'service_name' => '1年质保']);

        // --- 商品2：多规格（颜色+容量）---
        $goods2 = Goods::firstOrCreate(
            ['goods_name' => '智能手表 S3'],
            [
                'category_id'     => $cateDigital->id,
                'goods_image'     => '/images/goods/watch-s3.jpg',
                'selling_point'   => 'AMOLED屏幕 | 血氧监测 | IP68防水',
                'spec_type'       => 20, // 多规格
                'content'         => '<p>智能手表 S3，1.43英寸AMOLED屏幕，支持血氧、心率、睡眠监测。</p><p>IP68级防水，游泳佩戴无忧。</p>',
                'goods_price_min' => 899.00,
                'line_price_min'  => 1299.00,
                'goods_sales'     => 3560,
                'goods_status'    => 10,
                'sort'            => 2,
            ]
        );
        GoodsImage::firstOrCreate(['goods_id' => $goods2->id, 'image_url' => '/images/goods/watch-s3-1.jpg'], ['sort' => 1]);
        GoodsImage::firstOrCreate(['goods_id' => $goods2->id, 'image_url' => '/images/goods/watch-s3-2.jpg'], ['sort' => 2]);
        GoodsImage::firstOrCreate(['goods_id' => $goods2->id, 'image_url' => '/images/goods/watch-s3-3.jpg'], ['sort' => 3]);

        // 规格组：颜色
        $specColor = GoodsSpec::firstOrCreate(
            ['goods_id' => $goods2->id, 'spec_name' => '颜色'],
            ['sort' => 1]
        );
        $svBlack  = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specColor->id, 'spec_value' => '曜石黑'], ['sort' => 1]);
        $svSilver = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specColor->id, 'spec_value' => '星光银'], ['sort' => 2]);
        $svBlue   = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specColor->id, 'spec_value' => '深海蓝'], ['sort' => 3]);

        // 规格组：表带
        $specStrap = GoodsSpec::firstOrCreate(
            ['goods_id' => $goods2->id, 'spec_name' => '表带'],
            ['sort' => 2]
        );
        $svSilicone = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specStrap->id, 'spec_value' => '硅胶表带'], ['sort' => 1]);
        $svLeather  = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specStrap->id, 'spec_value' => '真皮表带'], ['sort' => 2]);

        // SKU 组合
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods2->id, 'sku_spec_ids' => "{$svBlack->id},{$svSilicone->id}"],
            ['goods_price' => 899.00, 'line_price' => 1299.00, 'stock' => 200, 'goods_weight' => 0.15, 'goods_no' => 'WS3-BK-SL']
        );
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods2->id, 'sku_spec_ids' => "{$svBlack->id},{$svLeather->id}"],
            ['goods_price' => 999.00, 'line_price' => 1399.00, 'stock' => 150, 'goods_weight' => 0.18, 'goods_no' => 'WS3-BK-LT']
        );
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods2->id, 'sku_spec_ids' => "{$svSilver->id},{$svSilicone->id}"],
            ['goods_price' => 899.00, 'line_price' => 1299.00, 'stock' => 180, 'goods_weight' => 0.15, 'goods_no' => 'WS3-SV-SL']
        );
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods2->id, 'sku_spec_ids' => "{$svSilver->id},{$svLeather->id}"],
            ['goods_price' => 999.00, 'line_price' => 1399.00, 'stock' => 120, 'goods_weight' => 0.18, 'goods_no' => 'WS3-SV-LT']
        );
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods2->id, 'sku_spec_ids' => "{$svBlue->id},{$svSilicone->id}"],
            ['goods_price' => 899.00, 'line_price' => 1299.00, 'stock' => 160, 'goods_weight' => 0.15, 'goods_no' => 'WS3-BL-SL']
        );
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods2->id, 'sku_spec_ids' => "{$svBlue->id},{$svLeather->id}"],
            ['goods_price' => 999.00, 'line_price' => 1399.00, 'stock' => 100, 'goods_weight' => 0.18, 'goods_no' => 'WS3-BL-LT']
        );

        GoodsService::firstOrCreate(['goods_id' => $goods2->id, 'service_name' => '7天无理由退换']);
        GoodsService::firstOrCreate(['goods_id' => $goods2->id, 'service_name' => '2年质保']);
        GoodsService::firstOrCreate(['goods_id' => $goods2->id, 'service_name' => '免费贴膜']);

        // --- 商品3：单规格 ---
        $goods3 = Goods::firstOrCreate(
            ['goods_name' => '纯棉圆领T恤'],
            [
                'category_id'     => $cateClothing->id,
                'goods_image'     => '/images/goods/tshirt.jpg',
                'selling_point'   => '100%纯棉 | 亲肤透气 | 多色可选',
                'spec_type'       => 10,
                'content'         => '<p>精选新疆长绒棉，柔软亲肤，透气不闷汗。</p>',
                'goods_price_min' => 79.00,
                'line_price_min'  => 159.00,
                'goods_sales'     => 5600,
                'goods_status'    => 10,
                'sort'            => 3,
            ]
        );
        GoodsImage::firstOrCreate(['goods_id' => $goods3->id, 'image_url' => '/images/goods/tshirt-1.jpg'], ['sort' => 1]);
        GoodsImage::firstOrCreate(['goods_id' => $goods3->id, 'image_url' => '/images/goods/tshirt-2.jpg'], ['sort' => 2]);
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods3->id, 'sku_spec_ids' => ''],
            ['goods_price' => 79.00, 'line_price' => 159.00, 'stock' => 2000, 'goods_weight' => 0.20, 'goods_no' => 'TS20240001']
        );
        GoodsService::firstOrCreate(['goods_id' => $goods3->id, 'service_name' => '7天无理由退换']);

        // --- 商品4：多规格（尺码）---
        $goods4 = Goods::firstOrCreate(
            ['goods_name' => '轻薄羽绒服'],
            [
                'category_id'     => $cateClothing->id,
                'goods_image'     => '/images/goods/down-jacket.jpg',
                'selling_point'   => '90%白鹅绒 | 轻薄保暖 | 可收纳',
                'spec_type'       => 20,
                'content'         => '<p>90%白鹅绒填充，蓬松度700+，轻薄保暖不臃肿。</p>',
                'goods_price_min' => 399.00,
                'line_price_min'  => 899.00,
                'goods_sales'     => 2100,
                'goods_status'    => 10,
                'sort'            => 4,
            ]
        );
        GoodsImage::firstOrCreate(['goods_id' => $goods4->id, 'image_url' => '/images/goods/down-jacket-1.jpg'], ['sort' => 1]);
        GoodsImage::firstOrCreate(['goods_id' => $goods4->id, 'image_url' => '/images/goods/down-jacket-2.jpg'], ['sort' => 2]);

        $specSize = GoodsSpec::firstOrCreate(
            ['goods_id' => $goods4->id, 'spec_name' => '尺码'],
            ['sort' => 1]
        );
        $svM = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specSize->id, 'spec_value' => 'M'], ['sort' => 1]);
        $svL = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specSize->id, 'spec_value' => 'L'], ['sort' => 2]);
        $svXL = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specSize->id, 'spec_value' => 'XL'], ['sort' => 3]);
        $svXXL = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specSize->id, 'spec_value' => 'XXL'], ['sort' => 4]);

        GoodsSku::firstOrCreate(
            ['goods_id' => $goods4->id, 'sku_spec_ids' => (string) $svM->id],
            ['goods_price' => 399.00, 'line_price' => 899.00, 'stock' => 300, 'goods_weight' => 0.50, 'goods_no' => 'DJ-M']
        );
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods4->id, 'sku_spec_ids' => (string) $svL->id],
            ['goods_price' => 399.00, 'line_price' => 899.00, 'stock' => 350, 'goods_weight' => 0.55, 'goods_no' => 'DJ-L']
        );
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods4->id, 'sku_spec_ids' => (string) $svXL->id],
            ['goods_price' => 399.00, 'line_price' => 899.00, 'stock' => 280, 'goods_weight' => 0.60, 'goods_no' => 'DJ-XL']
        );
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods4->id, 'sku_spec_ids' => (string) $svXXL->id],
            ['goods_price' => 399.00, 'line_price' => 899.00, 'stock' => 200, 'goods_weight' => 0.65, 'goods_no' => 'DJ-XXL']
        );
        GoodsService::firstOrCreate(['goods_id' => $goods4->id, 'service_name' => '7天无理由退换']);

        // --- 商品5：单规格 ---
        $goods5 = Goods::firstOrCreate(
            ['goods_name' => '有机坚果礼盒'],
            [
                'category_id'     => $cateFood->id,
                'goods_image'     => '/images/goods/nuts-gift.jpg',
                'selling_point'   => '6种坚果 | 每日一包 | 送礼佳品',
                'spec_type'       => 10,
                'content'         => '<p>精选6种优质坚果：核桃、巴旦木、腰果、榛子、夏威夷果、开心果。</p>',
                'goods_price_min' => 168.00,
                'line_price_min'  => 298.00,
                'goods_sales'     => 890,
                'goods_status'    => 10,
                'sort'            => 5,
            ]
        );
        GoodsImage::firstOrCreate(['goods_id' => $goods5->id, 'image_url' => '/images/goods/nuts-gift-1.jpg'], ['sort' => 1]);
        GoodsImage::firstOrCreate(['goods_id' => $goods5->id, 'image_url' => '/images/goods/nuts-gift-2.jpg'], ['sort' => 2]);
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods5->id, 'sku_spec_ids' => ''],
            ['goods_price' => 168.00, 'line_price' => 298.00, 'stock' => 600, 'goods_weight' => 1.50, 'goods_no' => 'NG20240001']
        );
        GoodsService::firstOrCreate(['goods_id' => $goods5->id, 'service_name' => '破损包赔']);

        // --- 商品6：单规格 ---
        $goods6 = Goods::firstOrCreate(
            ['goods_name' => '便携充电宝 20000mAh'],
            [
                'category_id'     => $cateDigital->id,
                'goods_image'     => '/images/goods/powerbank.jpg',
                'selling_point'   => '20000mAh | 22.5W快充 | 轻薄便携',
                'spec_type'       => 10,
                'content'         => '<p>20000mAh大容量，支持22.5W超级快充，可同时充3台设备。</p>',
                'goods_price_min' => 129.00,
                'line_price_min'  => 199.00,
                'goods_sales'     => 4300,
                'goods_status'    => 10,
                'sort'            => 6,
            ]
        );
        GoodsImage::firstOrCreate(['goods_id' => $goods6->id, 'image_url' => '/images/goods/powerbank-1.jpg'], ['sort' => 1]);
        GoodsImage::firstOrCreate(['goods_id' => $goods6->id, 'image_url' => '/images/goods/powerbank-2.jpg'], ['sort' => 2]);
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods6->id, 'sku_spec_ids' => ''],
            ['goods_price' => 129.00, 'line_price' => 199.00, 'stock' => 800, 'goods_weight' => 0.35, 'goods_no' => 'PB20240001']
        );
        GoodsService::firstOrCreate(['goods_id' => $goods6->id, 'service_name' => '1年质保']);

        // --- 商品7：多规格（口味）---
        $goods7 = Goods::firstOrCreate(
            ['goods_name' => '进口咖啡豆'],
            [
                'category_id'     => $cateFood->id,
                'goods_image'     => '/images/goods/coffee-beans.jpg',
                'selling_point'   => '阿拉比卡 | 中度烘焙 | 新鲜烘焙',
                'spec_type'       => 20,
                'content'         => '<p>精选哥伦比亚阿拉比卡咖啡豆，中度烘焙，酸苦平衡，回甘明显。</p>',
                'goods_price_min' => 68.00,
                'line_price_min'  => 128.00,
                'goods_sales'     => 1500,
                'goods_status'    => 10,
                'sort'            => 7,
            ]
        );
        GoodsImage::firstOrCreate(['goods_id' => $goods7->id, 'image_url' => '/images/goods/coffee-beans-1.jpg'], ['sort' => 1]);

        $specFlavor = GoodsSpec::firstOrCreate(
            ['goods_id' => $goods7->id, 'spec_name' => '口味'],
            ['sort' => 1]
        );
        $svNutty    = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specFlavor->id, 'spec_value' => '坚果风味'], ['sort' => 1]);
        $svChoco    = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specFlavor->id, 'spec_value' => '巧克力风味'], ['sort' => 2]);
        $svFruity   = GoodsSpecValue::firstOrCreate(['goods_spec_id' => $specFlavor->id, 'spec_value' => '果香风味'], ['sort' => 3]);

        GoodsSku::firstOrCreate(
            ['goods_id' => $goods7->id, 'sku_spec_ids' => (string) $svNutty->id],
            ['goods_price' => 68.00, 'line_price' => 128.00, 'stock' => 400, 'goods_weight' => 0.50, 'goods_no' => 'CB-NUT']
        );
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods7->id, 'sku_spec_ids' => (string) $svChoco->id],
            ['goods_price' => 72.00, 'line_price' => 128.00, 'stock' => 350, 'goods_weight' => 0.50, 'goods_no' => 'CB-CHO']
        );
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods7->id, 'sku_spec_ids' => (string) $svFruity->id],
            ['goods_price' => 78.00, 'line_price' => 128.00, 'stock' => 300, 'goods_weight' => 0.50, 'goods_no' => 'CB-FRU']
        );
        GoodsService::firstOrCreate(['goods_id' => $goods7->id, 'service_name' => '7天无理由退换']);

        // --- 商品8：单规格 ---
        $goods8 = Goods::firstOrCreate(
            ['goods_name' => '运动跑鞋 Air'],
            [
                'category_id'     => $cateClothing->id,
                'goods_image'     => '/images/goods/running-shoes.jpg',
                'selling_point'   => '超轻透气 | 缓震回弹 | 防滑耐磨',
                'spec_type'       => 10,
                'content'         => '<p>超轻透气网面，中底缓震科技，回弹率高达65%。</p>',
                'goods_price_min' => 299.00,
                'line_price_min'  => 599.00,
                'goods_sales'     => 7800,
                'goods_status'    => 10,
                'sort'            => 8,
            ]
        );
        GoodsImage::firstOrCreate(['goods_id' => $goods8->id, 'image_url' => '/images/goods/running-shoes-1.jpg'], ['sort' => 1]);
        GoodsImage::firstOrCreate(['goods_id' => $goods8->id, 'image_url' => '/images/goods/running-shoes-2.jpg'], ['sort' => 2]);
        GoodsImage::firstOrCreate(['goods_id' => $goods8->id, 'image_url' => '/images/goods/running-shoes-3.jpg'], ['sort' => 3]);
        GoodsSku::firstOrCreate(
            ['goods_id' => $goods8->id, 'sku_spec_ids' => ''],
            ['goods_price' => 299.00, 'line_price' => 599.00, 'stock' => 1200, 'goods_weight' => 0.80, 'goods_no' => 'RS20240001']
        );
        GoodsService::firstOrCreate(['goods_id' => $goods8->id, 'service_name' => '7天无理由退换']);
        GoodsService::firstOrCreate(['goods_id' => $goods8->id, 'service_name' => '30天质保']);

        // ============================================================
        // 5. 优惠券
        // ============================================================
        Coupon::firstOrCreate(
            ['name' => '新人专享券'],
            [
                'coupon_type'  => 10, // 满减
                'reduce_price' => 20.00,
                'discount'     => 0,
                'min_price'    => 99.00,
                'expire_type'  => 20, // 领取后N天过期
                'expire_day'   => 7,
                'start_time'   => null,
                'end_time'     => null,
                'describe'     => '新用户专享，满99减20',
                'apply_range'  => 10, // 全场通用
                'total_num'    => 1000,
                'receive_num'  => 0,
                'sort'         => 1,
                'status'       => 1,
            ]
        );

        Coupon::firstOrCreate(
            ['name' => '满200减30'],
            [
                'coupon_type'  => 10,
                'reduce_price' => 30.00,
                'discount'     => 0,
                'min_price'    => 200.00,
                'expire_type'  => 10, // 固定时间
                'expire_day'   => 0,
                'start_time'   => '2026-05-01 00:00:00',
                'end_time'     => '2026-12-31 23:59:59',
                'describe'     => '全场满200减30',
                'apply_range'  => 10,
                'total_num'    => 500,
                'receive_num'  => 0,
                'sort'         => 2,
                'status'       => 1,
            ]
        );

        Coupon::firstOrCreate(
            ['name' => '数码专场9折'],
            [
                'coupon_type'  => 20, // 折扣
                'reduce_price' => 0,
                'discount'     => 0.90, // 9折
                'min_price'    => 500.00,
                'expire_type'  => 10,
                'expire_day'   => 0,
                'start_time'   => '2026-05-01 00:00:00',
                'end_time'     => '2026-08-31 23:59:59',
                'describe'     => '数码产品满500享9折',
                'apply_range'  => 20, // 指定分类
                'total_num'    => 300,
                'receive_num'  => 0,
                'sort'         => 3,
                'status'       => 1,
            ]
        );

        Coupon::firstOrCreate(
            ['name' => '满500减80'],
            [
                'coupon_type'  => 10,
                'reduce_price' => 80.00,
                'discount'     => 0,
                'min_price'    => 500.00,
                'expire_type'  => 10,
                'expire_day'   => 0,
                'start_time'   => '2026-05-01 00:00:00',
                'end_time'     => '2026-12-31 23:59:59',
                'describe'     => '全场满500减80',
                'apply_range'  => 10,
                'total_num'    => 200,
                'receive_num'  => 0,
                'sort'         => 4,
                'status'       => 1,
            ]
        );

        Coupon::firstOrCreate(
            ['name' => '首单立减10元'],
            [
                'coupon_type'  => 10,
                'reduce_price' => 10.00,
                'discount'     => 0,
                'min_price'    => 50.00,
                'expire_type'  => 20,
                'expire_day'   => 30,
                'start_time'   => null,
                'end_time'     => null,
                'describe'     => '首单满50立减10元',
                'apply_range'  => 10,
                'total_num'    => 2000,
                'receive_num'  => 0,
                'sort'         => 5,
                'status'       => 1,
            ]
        );

        // ============================================================
        // 6. 省市区数据
        // ============================================================
        $provinceGD = Region::firstOrCreate(
            ['name' => '广东省', 'parent_id' => 0, 'level' => 1],
            ['initial' => 'G']
        );
        $provinceBJ = Region::firstOrCreate(
            ['name' => '北京市', 'parent_id' => 0, 'level' => 1],
            ['initial' => 'B']
        );
        $provinceSH = Region::firstOrCreate(
            ['name' => '上海市', 'parent_id' => 0, 'level' => 1],
            ['initial' => 'S']
        );
        $provinceZJ = Region::firstOrCreate(
            ['name' => '浙江省', 'parent_id' => 0, 'level' => 1],
            ['initial' => 'Z']
        );

        // 广东省城市
        $citySZ = Region::firstOrCreate(
            ['name' => '深圳市', 'parent_id' => $provinceGD->id, 'level' => 2],
            ['initial' => 'S']
        );
        $cityGZ = Region::firstOrCreate(
            ['name' => '广州市', 'parent_id' => $provinceGD->id, 'level' => 2],
            ['initial' => 'G']
        );
        $cityDG = Region::firstOrCreate(
            ['name' => '东莞市', 'parent_id' => $provinceGD->id, 'level' => 2],
            ['initial' => 'D']
        );

        // 深圳区
        Region::firstOrCreate(['name' => '南山区', 'parent_id' => $citySZ->id, 'level' => 3], ['initial' => 'N']);
        Region::firstOrCreate(['name' => '福田区', 'parent_id' => $citySZ->id, 'level' => 3], ['initial' => 'F']);
        Region::firstOrCreate(['name' => '宝安区', 'parent_id' => $citySZ->id, 'level' => 3], ['initial' => 'B']);

        // 广州区
        Region::firstOrCreate(['name' => '天河区', 'parent_id' => $cityGZ->id, 'level' => 3], ['initial' => 'T']);
        Region::firstOrCreate(['name' => '海珠区', 'parent_id' => $cityGZ->id, 'level' => 3], ['initial' => 'H']);

        // 北京市区
        Region::firstOrCreate(['name' => '朝阳区', 'parent_id' => $provinceBJ->id, 'level' => 2], ['initial' => 'C']);
        Region::firstOrCreate(['name' => '海淀区', 'parent_id' => $provinceBJ->id, 'level' => 2], ['initial' => 'H']);

        // 上海市区
        Region::firstOrCreate(['name' => '浦东新区', 'parent_id' => $provinceSH->id, 'level' => 2], ['initial' => 'P']);
        Region::firstOrCreate(['name' => '徐汇区', 'parent_id' => $provinceSH->id, 'level' => 2], ['initial' => 'X']);

        // 浙江省城市
        $cityHZ = Region::firstOrCreate(
            ['name' => '杭州市', 'parent_id' => $provinceZJ->id, 'level' => 2],
            ['initial' => 'H']
        );
        Region::firstOrCreate(['name' => '西湖区', 'parent_id' => $cityHZ->id, 'level' => 3], ['initial' => 'X']);
        Region::firstOrCreate(['name' => '滨江区', 'parent_id' => $cityHZ->id, 'level' => 3], ['initial' => 'B']);

        // ============================================================
        // 7. 商城基础设置
        // ============================================================
        $storeSettings = [
            ['key' => 'store_name',        'value' => 'Laravel Shop 商城'],
            ['key' => 'store_logo',        'value' => '/images/logo.png'],
            ['key' => 'store_desc',        'value' => '品质生活，从这里开始'],
            ['key' => 'contact_phone',     'value' => '400-888-8888'],
            ['key' => 'contact_email',     'value' => 'service@laravelshop.com'],
            ['key' => 'service_wechat',    'value' => 'LaravelShop_Official'],
            ['key' => 'express_price',     'value' => '0'],
            ['key' => 'free_express_min',  'value' => '99'],
            ['key' => 'points_ratio',      'value' => '100'], // 100积分抵1元
            ['key' => 'points_max_deduct', 'value' => '50'],  // 最多抵扣50元
            ['key' => 'is_open',           'value' => '1'],
            ['key' => 'copyright',         'value' => 'Copyright 2026 Laravel Shop. All rights reserved.'],
            ['key' => 'icp',               'value' => '粤ICP备2024000001号'],
        ];
        foreach ($storeSettings as $setting) {
            StoreSetting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        // ============================================================
        // 8. 文章分类 & 文章
        // ============================================================
        $artCateHelp = ArticleCategory::firstOrCreate(
            ['name' => '帮助中心'],
            ['sort' => 1]
        );
        $artCateNews = ArticleCategory::firstOrCreate(
            ['name' => '商城公告'],
            ['sort' => 2]
        );

        Article::firstOrCreate(
            ['title' => '新用户注册指南'],
            [
                'content'     => '<p>欢迎来到 Laravel Shop！注册账号即可享受会员专属优惠。</p><p>步骤一：点击右上角"注册"按钮。</p><p>步骤二：输入手机号获取验证码。</p><p>步骤三：设置密码完成注册。</p>',
                'category_id' => $artCateHelp->id,
                'cover'       => '',
                'sort'        => 1,
                'status'      => 1,
            ]
        );

        Article::firstOrCreate(
            ['title' => '如何下单购买'],
            [
                'content'     => '<p>下单流程：</p><p>1. 选择商品加入购物车</p><p>2. 进入购物车确认商品</p><p>3. 填写收货地址</p><p>4. 选择支付方式完成付款</p>',
                'category_id' => $artCateHelp->id,
                'cover'       => '',
                'sort'        => 2,
                'status'      => 1,
            ]
        );

        Article::firstOrCreate(
            ['title' => '退换货政策'],
            [
                'content'     => '<p>我们承诺7天无理由退换货。</p><p>退换货条件：商品未经使用、包装完好、不影响二次销售。</p><p>退款将在收到退货后3个工作日内原路返回。</p>',
                'category_id' => $artCateHelp->id,
                'cover'       => '',
                'sort'        => 3,
                'status'      => 1,
            ]
        );

        Article::firstOrCreate(
            ['title' => '积分规则说明'],
            [
                'content'     => '<p>消费1元可获得1积分，100积分可抵扣1元。</p><p>积分有效期为1年，过期自动清零。</p>',
                'category_id' => $artCateHelp->id,
                'cover'       => '',
                'sort'        => 4,
                'status'      => 1,
            ]
        );

        Article::firstOrCreate(
            ['title' => '618年中大促即将开启'],
            [
                'content'     => '<p>6月18日-6月20日，全场低至5折！</p><p>更有满减优惠券限量发放，先到先得！</p>',
                'category_id' => $artCateNews->id,
                'cover'       => '',
                'sort'        => 1,
                'status'      => 1,
            ]
        );

        Article::firstOrCreate(
            ['title' => '商城系统升级维护通知'],
            [
                'content'     => '<p>为了提供更好的服务，商城将于6月1日凌晨2:00-4:00进行系统升级维护，届时可能无法正常访问，敬请谅解。</p>',
                'category_id' => $artCateNews->id,
                'cover'       => '',
                'sort'        => 2,
                'status'      => 1,
            ]
        );
    }
}

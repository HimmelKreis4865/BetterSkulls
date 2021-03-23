<?php

namespace HimmelKreis4865\BetterSkulls;

use pocketmine\block\Block;
use pocketmine\block\Skull;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use ReflectionClass;
use ReflectionException;
use function base64_decode;
use function var_dump;

class SkullBlock extends Skull {
	/** @var array $directions */
	private $directions = [
		0 => 180,
		1 => 202.5,
		2 => 225,
		3 => 247.5,
		4 => 270,
		5 => 292.5,
		6 => 315,
		7 => 337.5,
		8 => 0,
		9 => 22.5,
		10 => 45,
		11 => 67.5,
		12 => 90,
		13 => 112.5,
		14 => 135,
		15 => 157.5,
	];
	
	/**
	 * @param Item $item
	 * @param Block $blockReplace
	 * @param Block $blockClicked
	 * @param int $face
	 * @param Vector3 $clickVector
	 * @param Player|null $player
	 *
	 * @return bool
	 *
	 * @throws ReflectionException
	 */
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool {
		if ($face !== Vector3::SIDE_UP) return false;
		
		if (!parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player)) return false;
		
		if (!$item->hasCustomBlockData() or !($tag = $item->getCustomBlockData())->hasTag("skull_data", StringTag::class)) return true;
		
		if (!($tile = $this->getLevelNonNull()->getTile($this->asVector3())) instanceof \pocketmine\tile\Skull) return true;
		$ref = new ReflectionClass($tile);
		$property = $ref->getProperty("skullRotation");
		$property->setAccessible(true);
		$yaw = $property->getValue($tile);
		
		$data = $tag->getString("skull_data");
		
		for ($i = 1; $i < 32; $i++) {
			if ($tag->hasTag("skull_data_" . $i, StringTag::class)) $data .= $tag->getString("skull_data_" . $i);
		}
		
		$data = base64_decode($data);
		
		$position = $this->add(0.5, -0.01, 0.5);
		$nbt = Entity::createBaseNBT($position, null, $this->directions[$yaw]);
		
		$nbt->setTag(new CompoundTag("Skin", [
			new StringTag("Name", "Custom_Head_Layer"),
			new ByteArrayTag("Data", $data),
			new ByteArrayTag("CapeData", ""),
			new StringTag("GeometryName", "geometry.skull"),
			new ByteArrayTag("GeometryData", BetterSkulls::GEOMETRY)
		]));
		
		$nbt->setString("skull_name", $tag->getString("skull_name"));
		
		$skull = new SkullEntity($this->getLevelNonNull(), $nbt);
		$skull->setImmobile();
		$skull->spawnToAll();
		return true;
	}
	
	/**
	 * Returns an empty array to prevent bugs
	 *
	 * @api
	 *
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item): array {
		return [];
	}
}
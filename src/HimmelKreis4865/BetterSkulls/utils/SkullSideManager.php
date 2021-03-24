<?php

namespace HimmelKreis4865\BetterSkulls\utils;

use pocketmine\math\Vector3;
use function var_dump;

final class SkullSideManager {
	
	/** @var array $directions */
	private static $directions = [
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
	
	public static function addAdditions(int $face, int $skullRotation): array {
		if ($face === Vector3::SIDE_UP) return [self::$directions[$skullRotation], new Vector3(0.5, 0, 0.5)];
		var_dump($face);
		$baseVector = new Vector3(0, 0.23, 0);
		
		switch ($face) {
			case Vector3::SIDE_SOUTH:
				$baseVector->x += 0.5;
				$baseVector->z += 0.25;
				break;
				
			case Vector3::SIDE_NORTH:
				$baseVector->x += 0.5;
				$baseVector->z += 0.75;
				break;
			case Vector3::SIDE_EAST:
				$baseVector->x += 0.25;
				$baseVector->z += 0.5;
				break;
				
			case Vector3::SIDE_WEST:
				$baseVector->x += 0.75;
				$baseVector->z += 0.5;
				break;
		}
		
		return [self::getFaceYaw($face), $baseVector];
	}
	
	private static function getFaceYaw(int $face): int {
		switch ($face) {
			case Vector3::SIDE_SOUTH:
				var_dump("south");
				return 0;
			case Vector3::SIDE_EAST:
				var_dump("east");
				return 270;
			case Vector3::SIDE_WEST:
				var_dump("west");
				return 90;
		}
		var_dump("north");
		return 180;
	}
}
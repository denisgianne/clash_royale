select
-- tl.level, t.name, tl.damage_per_second, tl.hitpoints
distinct( t.name ), t.rarity as rarity,
case t.rarity
	when "common" then IF( max(tl.level) > 11, "ALL", MAX(tl.level))
	when "rare" then IF( max(tl.level) > 9, "ALL", MAX(tl.level))
	when "epic" then IF( max(tl.level) > 7, "ALL", MAX(tl.level))
	when "legendary" then IF( max(tl.level) > 5, "ALL", MAX(tl.level))
end as max_level,
max(tl.hitpoints) as max_hitpoints
from `troops_levels` as tl
LEFT JOIN troops as t ON
	tl.id_troop=t.id
-- WHERE `hitpoints` BETWEEN 202 AND 221
-- WHERE `hitpoints` < 221 -- flecha 8
-- WHERE `hitpoints` < 476 -- fogo 5
WHERE `hitpoints` < 520 -- fogo 6

group by t.name, t.rarity

order by max_level, rarity, max_hitpoints
<?php

use yii\db\Migration;

/**
 * Class m200219_015608_add_dream_categories
 */
class m200219_015608_add_dream_categories extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->safeDown();

		$this->execute("
			INSERT INTO
				dj.dream_category(name)
			VALUES
				('Driving or Vehicles'),
				('Food and Eating'),
				('Watching a Movie'),
				('Mirrors'),
				('Storms'),
				('Floods or Tidal Waves'),
				('Earthquakes'),
				('Fire'),
				('Finding Money'),
				('Flying (Airplane)'),
				('Flying (Magical)'),
				('About to Fall'),
				('Falling'),
				('Seeing Flying Objects Crash'),
				('Swimming'),
				('Underwater'),
				('School or Studying'),
				('Failing an Exam'),
				('Repeated Attempts at Task'),
				('Being Late'),
				('Needing to Urinate or Find Toilet'),
				('Losing Teeth'),
				('Being Nude'),
				('Being Inappropriately Dressed'),
				('Sleep Paralysis'),
				('Unable to Breathe'),
				('Incarceration or Being Restrained'),
				('Physical Illnesses'),
				('Mental Illnesses'),
				('Hospitals'),
				('Someone Having an Abortion'),
				('Seeing Oneself as Dead'),
				('Being Chased'),
				('Being Frozen from Fear'),
				('Killing Someone'),
				('Being Killed'),
				('Attacking Someone'),
				('Being Attacked'),
				('Animals or Pets'),
				('Part-human Creatures'),
				('Snakes'),
				('Insects or Spiders'),
				('Wild Beasts'),
				('Being an Animal'),
				('Being a Child'),
				('Being an Inanimate Object'),
				('Being Member of Opposite Sex'),
				('Speaking to Deceased Persons'),
				('Being with Friends'),
				('Being with Family'),
				('Going on Vacation'),
				('Home'),
				('Seeing a Face Up Close'),
				('Discovering a Room'),
				('Space Travel'),
				('Seeing UFO'),
				('Encounter with Extraterrestrials'),
				('Magical Powers'),
				('Sensing a Presence'),
				('Seeing an Angel'),
				('Sexual Experiences'),
				('Spiritual Experiences'),
				('Religious Vision'),
				('Encountering God or Similar')
		");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DELETE FROM dj.dream_category;");
    }
}

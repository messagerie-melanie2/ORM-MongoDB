<?php
/**
 * Ce fichier est développé pour la gestion de la librairie ORM
 * Cette Librairie permet d'accèder aux données sans avoir à implémenter de couche SQL
 * Des objets génériques vont permettre d'accèder et de mettre à jour les données
 *
 * ORM Copyright © 2015  PNE Annuaire et Messagerie/MEDDE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace ORM\Tests\Lib;

// Appel le namespace
use ORM\API\PHP;

/**
 * Génération de données aléatoire
 *
 * @author thomas
 *
 */
class Crud {

  /**
   * Création d'un event complet dans la base de données
   * Utilisation de faker
   * @return multitype:\ORM\API\PHP\Event boolean
   */
  public static function CreateFakerEvent() {
    // Génération de l'objet
    $event = new PHP\Event();

    $event->uid = uniqid().md5(time())."@TestORM";
    $event->calendar = Random::Calendar();
    $event->owner = Random::Owner();
    $event->title = Random::Summary();

    if (Random::bool()) {
      $event->description = Random::Description();
    }

    $event->start = Random::Start();
    $event->end = clone $event->start;
    $event->end->add(new DateInterval("PT" . Random::$faker->randomNumber(2) . "H"));

    if (Random::bool()) {
      $event->organizer->name = Random::$faker->name;
      $event->organizer->email = Random::$faker->email;

      $nbatt = rand(1, 5);
      $attendees = array();
      for ($i = 0; $i < $nbatt; $i++) {
        $attendee = new PHP\Attendee();
        $attendee->name = Random::$faker->name;
        $attendee->email = Random::$faker->email;
        $attendee->response = Random::$faker->randomElement(array($attendee::RESPONSE_ACCEPTED, $attendee::RESPONSE_DECLINED, $attendee::RESPONSE_IN_PROCESS, $attendee::RESPONSE_NEED_ACTION, $attendee::RESPONSE_TENTATIVE));
        $attendee->role = Random::$faker->randomElement(array($attendee::ROLE_CHAIR, $attendee::ROLE_NON_PARTICIPANT, $attendee::ROLE_OPT_PARTICIPANT, $attendee::ROLE_REQ_PARTICIPANT));
        $attendees[] = $attendee;
      }
      $event->attendees = $attendees;
    }

    if (Random::bool()) {
      $event->categories = array(Random::$faker->realText(rand(10, 15)), Random::$faker->realText(rand(10, 15)), Random::$faker->realText(rand(10, 15)));
    }

    $event->status = Random::$faker->randomElement(array($event::STATUS_CANCELLED, $event::STATUS_CONFIRMED, $event::STATUS_NONE, $event::STATUS_TENTATIVE));
    $event->class = Random::$faker->randomElement(array($event::CLASS_CONFIDENTIAL, $event::CLASS_PRIVATE, $event::CLASS_PUBLIC));

    if (Random::bool()) {
      $attachment = new PHP\Attachment();
      $attachment->type = $attachment::TYPE_URL;
      $attachment->data = Random::$faker->url;
      $event->attachments = array($attachment);
    }

    if (Random::bool()) {
      $event->recurrence->freq = PHP\Recurrence::FREQ_MONTHLY;
      $event->recurrence->count = 5;
    }

    $result = $event->insert();

    return array('result' => $result, 'event' => $event);
  }

  /**
   * Création d'un event dans la base de données
   * Utilise quelques données aléatoire plus être le plus rapide possible
   * @return multitype:\ORM\API\PHP\Event boolean
   */
  public static function CreateLightRandomEvent() {
    // Génération de l'objet
    $event = new PHP\Event();

    $val = rand(1, 100000000);
    $time = rand(1446332400, 1514674800);

    $event->uid = uniqid().md5(time())."@TestORM";
    $event->calendar = "user".$val;
    $event->owner = "user".$val;
    $event->title = "Summary".$val;

    if (Random::bool()) {
      $event->description = "Description $val : La cavalerie française pendant la Première Guerre mondiale a une participation relativement secondaire aux événements. Les combattants à cheval se révélant très vulnérables face à la puissance de feu de l'infanterie et de l'artillerie, les différentes unités de cette arme accomplissent essentiellement des missions d'auxiliaires pendant la « Grande Guerre » (de 1914 à 1919), même si le début du conflit correspond à son apogée en termes d'effectifs montés.

        Principalement déployée sur le front occidental, la cavalerie française participe aux opérations de l'été 1914, assurant surtout des missions de reconnaissance et de patrouille. Rapidement, les cavaliers combattent systématiquement démontés, tirant avec leur carabine. À partir de l'automne 1914, la guerre des tranchées a pour conséquence de diminuer fortement le rôle de la cavalerie : une partie des régiments abandonne ses chevaux, forme des « divisions de cavalerie à pied » et participe aux combats en tant que fantassins. La reprise de la guerre de mouvement en 1918 redonne à la cavalerie une utilité, comme infanterie montée.

      ";
    }
    $event->start = new \DateTime("@$time");
    $event->end = new \DateTime("@" . ($time + 3600));

    if (Random::bool()) {
      $event->organizer->name = "Organizer $val";
      $event->organizer->email = "organizer.$val@test.com";

      $nbatt = rand(1, 5);
      $attendees = array();
      for ($i = 0; $i < $nbatt; $i++) {
        $attendee = new PHP\Attendee();
        $attendee->name = "Attendee $i $val";
        $attendee->email = "attendee.$i.$val@test$i.com";
        $attendee->response = $attendee::RESPONSE_ACCEPTED;
        $attendee->role = $attendee::ROLE_REQ_PARTICIPANT;
        $attendees[] = $attendee;
      }
      $event->attendees = $attendees;
    }

    if (Random::bool()) {
      $event->categories = array("test1$val", "test2$val");
    }

    $event->status = $event::STATUS_CONFIRMED;
    $event->class = $event::CLASS_PUBLIC;

    if (Random::bool()) {
      $attachment = new PHP\Attachment();
      $attachment->type = $attachment::TYPE_URL;
      $attachment->data = "https://www.example$val.com/";
      $event->attachments = array($attachment);
    }

    if (Random::bool()) {
      $event->recurrence->freq = PHP\Recurrence::FREQ_MONTHLY;
      $event->recurrence->count = 5;
    }

    $result = $event->insert();
    return array('result' => $result, 'event' => $event);
  }
}


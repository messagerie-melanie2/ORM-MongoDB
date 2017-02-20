<?php

/**
 * Ce fichier est développé pour la gestion de la librairie ORM
 * Cette Librairie permet d'accèder aux données sans avoir à implémenter de couche SQL
 * Des objets génériques vont permettre d'accèder et de mettre à jour les données
 *
 * ORM Copyright © 2017  PNE Annuaire et Messagerie/MEDDE
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
use ORM\Core\Mapping\Operators;

/**
 * Génération de données aléatoire
 *
 * @author thomas
 *
 */
class Crud {

  private static $users = array(
      "bob.test",
      "dylan.test",
      "alfred.test",
      "jimmy.test",
      "john.test",
      "jack.test",
      "etienne.test",
      "norber.test",
      "barack.test",
      "tchin.test",
      "fred.test",
      "joe.test",
      "william.test",
      "billy.test",
      "robert.test",
      "chris.test",
      "edouard.test",
      "george.test",
      "nick.test",
      "ted.test",
      "sir.test",
      "johnny.test",
      "aurelien.test",
      "wesley.test",
      "raphael.test",
      "clement.test",
      "ludo.test",
      "tim.test",
      "tom.test",
      "thomas.test",
      "david.test",
      "marc.test"
  );

  private static $titles = array(
      "Cubillas de Cerrato",
      "Mary Kom (film)",
      "Forme (botanique) En botanique et en mycologie, la forme (du latin forma, souvent abrégé en « fo. » ou « f. ») est une entité ou taxon de rang inférieur à l’espèce et à la variété.",
      "Liste des sanctions de l'Union européenne à l'encontre de la Fédération de Russie",
      "Prix de l'Institut canadien de Québec",
      "Église Notre-Dame Saint-Louis (Lyon)",
      "Jean Gobet ean Gobet (Joseph, Paul, Louis Gobet) est un acteur français né le 20 juillet 1888 à Mornant et décédé le 29 avril 1980 à Créteil.",
      "Minor civil division",
      "Les metalleux (parfois orthographié métalleux) sont l'ensemble des gens adhérant à la culture de la musique metal en général",
      "Henri Lafontaine",
      "Commune de Laeva",
      "Jean-Paul Sèvres est un humoriste français.",
      "Sébastien Bruzzese, né le 1er mars 1989 à Liège, est un joueur de football belge qui évolue au poste de gardien de but. Depuis juillet 2015, il joue au FC Bruges, en première division belge.",
      "Canton de Callac",
      "L'église Sainte-Catherine est l'église de l'ancien monastère des Bénédictins de Vilnius. Cette église de style baroque domine la vieille ville de ses tours de couleur rose. Elle est située près de l'ancien couvent des Dominicains, à l'angle de la rue des Bénédictins et de la rue Saint-Ignace.",
      "Moutarde des champs",
      "Un pantalon est un vêtement unisexe porté sur la partie inférieure du corps, les deux jambes étant couvertes séparément.",
      "La rue Eugène-Jumin est une voie du 19e arrondissement de Paris, en France.",
      "Mohamed Ben Mohamed Ben Djilali Gherainia1, dit le Prince, né le 5 juillet 1891 à Affreville, ",
      "Commissaire européen au commerce",
      "Paradoxe de l'amitié",
      "Zalesie-Stefanowo est un village de Pologne, situé dans la gmina de Czyżew, dans le Powiat de Wysokie Mazowieckie, dans la voïvodie de Podlachie.",
      "Jan Balabán, né le 29 janvier 1961 à Šumperk et mort le 23 avril 2010 à Ostrava, est un écrivain, journaliste et traducteur tchèque.",
      "Scleromochlus est un reptile du Trias.",
      "Porte de Montmartre",
      "Raúl Troncoso"
  );

  private static $descriptions = array(
      "Cubillas de Cerrato est une commune espagnole de la province de Palencia, dans la communauté autonome de Castille-et-León.",
      "Mary Kom est un film biographique indien réalisé par Omung Kumar, sorti en 2014. Il relate la vie de la championne de boxe indienne Mary Kom, interprétée par Priyanka Chopra.",
      "Aspect taxinomique

La forme étant la plus petite coupure taxinomique dans la systématique et la classification du monde vivant, la plus proche de « l’individu » en présence.

Par exemple, quand nous disons que nous avons dans notre assiette la « forme blanche » du Champignon de Paris, nous faisons empiriquement la même division que le mycologue qui l’a nommée par le trinôme Agaricus bisporus fo. alba.

Plus encore que le rang variétal, le choix du rang formel indique que la population d’individus ainsi circonscrits ne diffère de l’espèce « type » que par un ou plusieurs caractères considérés comme mineurs sur un plan taxinomique (particularité morphologique, écologique, organoleptique, etc.), comme la « couleur blanche » dans notre exemple.
Aspect nomenclatural

La première fois qu’une espèce se voit attribuer une division au rang de forme, il y a réciproquement et automatiquement création d’une forme autonyme pour désigner l’espèce dont elle a été séparée au sens strict (stricto sensu), à présent considérée comme la « forme type », désignée par le trinôme : Agaricus bisporus fo. bisporus.
          ",
      "La liste des sanctions de l'Union européenne à l'encontre de la Fédération de Russie comprend une liste de personnes de citoyenneté russe ou ukrainienne interdites de visa pour pénétrer en Union européenne et frappés du gel de leurs avoirs éventuels en Union européenne. Elle comprend aussi une liste de compagnies et entreprises à qui il est interdit de commercer avec l'Union européenne. Ces deux listes sont élaborées à partir du 17 mars 2014 dans le cadre de sanctions à l'encontre de la Fédération de Russie pour son immixtion dans la crise ukrainienne de 2013-2014 et pour le rattachement (qualifié d'« annexion » par Bruxelles et de « retour » par Moscou) de la Crimée à la Fédération de Russie. Ces mesures ont été décidées pour la première fois le 17 mars 2014 par les ministres des Affaires étrangères (pour la France, Laurent Fabius) des vingt-huit pays membres de l'Union européenne réunis en conseil à Bruxelles, juste après le référendum des Criméens demandant leur rattachement à la Russie.

Cette liste de sanctions a été allongée le 21 mars 2014, le 28 avril 2014, le 12 mai 2014, les 12, 24 et 31 juillet 2014.",
      "Créé en 1979, le Prix de L'Institut Canadien de Québec est destiné à honorer une personnalité qui œuvre de façon exceptionnelle dans la région de Québec depuis au moins dix ans dans le secteur des arts et des lettres, que ce soit par la création artistique ou par la promotion de la culture.

Ce prix, remis par l'Institut canadien de Québec, est attribué à une personne toujours active dans le milieu culturel de façon à soutenir et promouvoir ses actions.

Le Prix de L'Institut Canadien de Québec est remis annuellement dans le cadre des Prix d'excellence des arts et de la culture en même temps qu'une bourse de 1 000 $.",
      "Notre-Dame Saint-Louis est une église affectée au culte catholique, située dans le quartier de Guillotière, dans le 7e arrondissement de Lyon, dont elle constitue le sanctuaire paroissial le plus ancien. Elle est située à l'intersection de la rue de la Madeleine et de la Grande rue de la Guillotière.",
      "Minor civil division (MCD) est un terme utilisé par l'United States Census Bureau pour désigner la division administrative la plus petite d'un comté, tel que les civil townships, ou les precincts (en).

Dans vingt États, toutes ou certaines MCD sont des unités d'usage gouvernementale : au Connecticut, Illinois, Indiana, Kansas, Maine, Massachusetts, Michigan, Minnesota, Missouri, Nebraska, New Hampshire, New Jersey, New York, Dakota du Nord, Ohio, Pennsylvania, Rhode Island, Dakota du Sud, Vermont, et Wisconsin. La plupart de ces MCD sont légalement désignées comme des villes ou des municipalités.

Dans les États n'ayant pas de MCD, le Census Bureau désigne des Census County Divisions (CCD). Dans les États utilisant les MCD, quand une portion de l'État n'est pas couverte par une MCD, le Census Bureau crée des entités additionnelles tel que les territoires non-organisés, qui est l'équivalent des MCD à des fins statistiques.",
      "Les metalleux (parfois orthographié métalleux) sont l'ensemble des gens adhérant à la culture de la musique metal en général, des musiciens de metal et de leurs fans. On parlait autrefois de hardos ou de hardeux (en référence au hard rock, terme qui désignait la musique metal dans son ensemble dans les années 80), mais ce terme est aujourd'hui tombé en désuétude. Les termes anglophones metalhead et headbanger sont aussi parfois utilisés en français pour désigner les metalleux. La culture metal est une sous-culture underground, c'est-à-dire qu'elle est tenue à l'écart des médias de masse et que ses tenants vivent en marge de la société.

De manière générale, les metalleux de tous les genres de metal portent des cheveux longs et des habits noirs. Ils revêtent souvent un t-shirt à l'effigie de l'un des groupes desquels ils sont fans. D'autres éléments de la mode des metalleux incluent des vêtements en cuir ou en jean, des accessoires en cuir (mitaines/gants, bracelets, colliers) ainsi que des clous, chaînes et anneaux métalliques portés sur tous types de pièces vestimentaires. Le piercing et le tatouage ne sont pas rares mais propres à des genres relativement modernes. D'autres façons pour les metalleux d'affirmer leur appartenance à la culture metal est de collectionner les albums musicaux et d'autres produits dérivés ainsi que d'assister à des concerts.

La culture metal s'est d'abord développée au Royaume-Uni et aux États-Unis, mais s'est rapidement répandue sur toute la planète et s'est beaucoup diversifiée. La culture entourant la musique metal a fortement aidé cette dernière à durer plus longtemps que les autres styles de musique rock1. Bien que souvent associés aux jeunes, les metalleux sont présents dans les tranches d'âge allant jusqu'à la quarantaine et même la soixantaine, plusieurs des premiers fans de heavy metal des années 1980 ayant vieilli sans abandonner leur côté metal. La plupart des metalleux sont issus de la classe moyenne et ont un bon bagage culturel. Cependant, il y a des metalleux sur l'ensemble de la planète issus d'origines différentes. En effet, la culture metal se répand dans tous les pays, y compris les plus stricts et conservateurs.

La culture metal est essentiellement masculine. Cependant, de plus en plus, le metal a un public et des membres de groupe féminins.

Les metalleux sont perçus par le grand public de manière stéréotypée comme étant des jeunes se rebellant en affichant leur côté anti-social et pour leur goût de la musique bruyante, image véhiculée par quelques films et émissions télévisées. Néanmoins, la culture metal fait partie intégrante de la culture populaire.",
      "Louis-Henri-Marie Thomas Lafontaine dit Henri Lafontaine ou Lafontaine est un acteur français né le 28 mai 1824 1 à Bordeaux et mort le 23 février 1898 à Versailles. Il est inhumé à Versailes au Cimetière Notre-Dame2.

          ",
      "La commune de Laeva (en estonien : Laeva vald) est une municipalité rurale estonienne appartenant à la région de Tartu. Elle s'étend sur une superficie de 233,2 km22. Elle a 805 habitants1(01.01.2012)3.",
      "Fin des années 1960, il vit cinq ou six ans de dérive, tout le monde le connaît, il a les cheveux noirs. Il raconte à qui veut l'entendre qu'il est le plus jeune humoriste de France ou bien le plus mauvais peintre de la planète ou encore le mime le plus inconnu du siècle… Il se retrouve à l'Olympia à présenter les Rolling Stones… Eddy Barclay le remarque et lui propose d'enregistrer un disque… Il s'enfuit en grimpant à un réverbère… Mais le show-biz est tenace et il se retrouve en studio.

Metteur en scène de Pendant les travaux la fête continue de Patrick Font et Philippe Val, Jean-Paul Sèvres s'installe dans un squat de la rue de l'Ouest au début des années 70 et crée le Merdic théâtre auquel Jacques Martin fera un large écho dans son émission de télévision. C'est là qu'il met le pied à l'étrier de nombreux jeunes artistes comme Jean-Jacques Peroni, Myriam Roustan et bien d'autres que l'on retrouvera plus tard au Petit théâtre de Philippe Bouvard.

Coluche, qui avait une sincère admiration pour lui, confiera un soir au Port du Salut qu'il est un des rares mecs de génie qu'il ait jamais rencontré.

Auteur de nombreuses pièces de café-théâtres il est coauteur avec Luis Rego et Didier Kaminka de Viens chez moi, j'habite chez une copine, il est également auteur de nombreuses chansons, il signe beaucoup de textes avec son ami le chanteur Éric Vincent.",
      "Sébastien Bruzzese débute le football à l'âge de six ans à Seraing-RUL. Après trois ans, il est repéré par le RFC Liège et rejoint son école de jeunes. Il y effectue toute sa formation et est intégré à l'effectif de l'équipe première en 2006, après la remontée du club en Division 3. Il est deuxième gardien et ne joue que trois rencontres en championnat durant la saison. C'est néanmoins suffisant pour lui permettre d'être appelé en équipe nationale des moins de 19 ans en avril 2007 et d'être ensuite recruté en fin de saison par le RSC Anderlecht, champion de Belgique1. Il y est quatrième dans la hiérarchie des gardiens derrière Daniel Zitka, Silvio Proto et Davy Schollen et doit se contenter de jouer avec l'équipe réserve. Il fait quelques apparitions sur le banc des réservistes en championnat après la blessure de Zitka et Proto étant prêté au Germinal Beerschot.",
      "Le canton de Callac est une division administrative française, située dans le département des Côtes-d'Armor et la région Bretagne. À la suite du redécoupage cantonal de 2014, les limites territoriales du canton sont remaniées. Le nombre de communes du canton passe de 11 à 28.",
      "Histoire

Les moines bénédictins sont arrivés de Nieswiez (aujourd'hui Niasvij en Biélorussie) à la fin des années 1620 et leur église de bois dédiée à sainte Catherine est consacrée en 1632. Au fil des ans, le monastère est agrandi et une nouvelle église est construite en pierre en 1650. Elle est réaménagée au début du XVIIIe siècle, après la guerre russo-polonaise, et reconstruite entre 1741 et 1773, après un incendie qui avait provoqué de graves dommages. Les pères bénédictins commandent à Szymon Czechowicz1 bon nombre de tableaux.

L'intérieur de l'église, achevé dans les années 1760, est considéré comme un chef-d'œuvre du baroque tardif. C'est à cette époque que sont construites les tours rococo de la façade par l'architecte germano-balte Johann Christoph Glaubitz. Le monastère possédait une bibliothèque immense (en partie conservée aujourd'hui à la Bibliothèque nationale de Lituanie (en)). Lorsque Vilna (nom officiel de Vilnius à l'époque de l'Empire russe) est occupée par les troupes de Napoléon à partir de juin 1812, l'église sert d'entrepôt et le monastère d'infirmerie. À la fin du XIXe siècle d'anciennes religieuses franciscaines du couvent Saint-Michel s'y installent pour soigner les malades. Elles laissent ensuite la place à un lycée de jeunes filles. Vilna est occupée par les troupes allemandes en 1915. C'est la fin de son appartenance factuelle à la Russie impériale. On ôte le buste de Pouchkine devant l'église que les Polonais remplacent par celui du compositeur polonais Moniuszko en 1922, lorsque Wilno devient le nom officiel de la ville sous le régime de la république de Pologne.

Les moines sont expulsés dans les nouvelles frontières de la Pologne en 1946, comme la majorité de leurs compatriotes de la ville et l'église est fermée par les autorités de la république socialiste soviétique de Lituanie et transformé en entrepôt des œuvres des musées locaux. Le monastère est transformé en complexe d'habitations. L'église est restituée à l'archidiocèse de Vilnius en 1990.",
      "La Moutarde des champs ou Sanve ou sénevé (Sinapis arvensis), est une plante annuelle herbacée de la famille des Brassicacées (aussi nommée Crucifères), placée souvent dans les mauvaises herbes (adventices), envahissant champs et jardins.",
      "Il doit son nom à celui du personnage de Commedia dell’arte, Pantalone (en français : Pantalon), dont le costume comporte ces culottes longues typiques1.

En argot : falzar, fendard (Genève), futal (ou fut’), froc, grimpant, etc.

Il s’ouvre au milieu sur le devant par une braguette ou sur les côtés par un pont.

Historiquement, le pantalon est lié à l’histoire de la domestication du cheval, étant indispensable pour le monter. Le pantalon moderne sera adopté vers 1850 sous le surnom tuyau de poêle. Il n’évolue que sur des détails depuis comme l’adjonction d’un revers sous l’impulsion de Édouard VII du Royaume-Uni en 1909, par exemple. C’est le sport qui en popularisera le port chez les femmes2.",
      "Description

La rue Eugène-Jumin est une voie publique située dans le 19e arrondissement de Paris. Elle débute au 95 rue Petit et se termine au 198 avenue Jean-Jaurès.",
      "Mohamed Ben Mohamed Ben Djilali Gherainia1, dit le Prince, né le 5 juillet 1891 à Affreville, aujourd'hui Khemis Miliana, en Algérie, et décédé le 22 juin1 ou le 14 octobre 19182 à Slobozia (Roumanie), est un tirailleur algérien, fait prisonnier par les Allemands pendant la Première Guerre mondiale, et envoyé dans un camp de prisonniers en Roumanie entre 1916 et 1918. Son engagement dans la résistance locale lui ont valu une notoriété en Roumanie et surtout dans la ville de Slobozia.",
      "Le Commissaire européen pour le commerce extérieur est le membre de la Commission européenne responsable de la politique commerciale extérieure.

Le commissaire est notamment la voix de l'Union européenne au sein d'organisations comme l'OMC.",
      "Le paradoxe de l'amitié est le phénomène découvert par le sociologue Scott L. Feld en 1991.

Pour un individu moyen, la plupart de ses amis ont plus d'amis que lui. L'explication de ce paradoxe repose sur un biais statistique, les personnes ayant un grand nombre d'amis ont une probabilité plus forte d'être incluse dans les amis d'une autre personne1.",
      "Source

    (en) Cet article est partiellement ou en totalité issu de l’article de Wikipédia en anglais intitulé « Zalesie-Stefanowo » (voir la liste des auteurs).
          ",
      "Jan Balabán, né le 29 janvier 1961 à Šumperk et mort le 23 avril 2010 à Ostrava, est un écrivain, journaliste et traducteur tchèque.

Jan Balabán vit depuis son enfance à Ostrava. Il étudie la littérature hongroise et anglaise à l'université Palacký à Olomouc. Il travaille après ses études comme traducteur et traduit notamment des textes de Howard Phillips Lovecraft et de Terry Eagleton vers le tchèque. Balabán écrit aussi des critiques sur les arts visuels dans des revues spécialisés et dans les journaux.

Pendant ses études, Balabán écrit des poèmes qui sont publiés dans la Revolver Revue. En collaboration avec le poète Petr Hruska, Balabán est l'éditeur de la revue littéraire Obracena strana mesice.

Le prose de Balabáns se caractérise par des attributions réalistes et par le sens pour le détail. Beaucoup de ses textes ont des attributions autobiographiques et sortent du genius loci de son lieu de résidence Ostrava.",
      "Les longues jambes de Scleromochlus nous apparaissent aujourd'hui comme étant typique d'animaux coureurs, ceci n'a pas toujours été le cas puisque le premier lien qui a été fait entre cette espèce et les ptérosaures l'a été par Friedrich von Huene, en 1914, qui y a vu une preuve de vie arboricole.",
      "La porte de Montmartre est une porte de Paris, en France, située dans le 18e arrondissement.
Situation

La porte de Montmartre est une petite porte de Paris située à 500 m à l'ouest de la porte de Clignancourt et 500 m à l'est de la porte de Saint-Ouen. Datant de la période de l'enceinte de Thiers, elle se trouve sur le boulevard Ney, à la jonction avec la rue du Poteau et l’avenue de la Porte-de-Montmartre.",
      "Raúl Troncoso Castillo (né le 27 avril 1935 à Santiago du Chili et décédé le 28 novembre 2004 (à 69 ans) dans la même ville), est un homme politique chilien. Ambassadeur en Italie de 1990 à 1992. Ministre de la Défense en 1998. Ministre de l'Intérieur de 1998 au 11 mars 2000.",
      "Le Prix des Drags est une course hippique de Steeple-chase se déroulant au mois de juin sur l'hippodrome d'Auteuil.

C'est une course de groupe II réservée aux chevaux de 5 ans et plus. Elle se court sur 4 400 mètres. L'allocation pour l'année 2007 est de 240 000 €."
  );

  private static $names = array(
      "Moriz Rosenthal",
      "Francesco Orsi",
      "Fedele Fedeli",
      "Raffaello Silvestrini",
      "Augusto Murri",
      "Ligne de Damoiseau Ellis",
      "Calvin Ellis",
      "Louis-Hyacinthe-Céleste Damoiseau",
      "Barack Obama",
      "Justin Trudeau",
      "Victor Ponta",
      "Xi Jinping",
      "Ma Ying-jeou",
      "Darejan Dadiani",
      "Beaver Run",
      "Bowman Creek",
      "Nelle Morton",
      "Mary Jane Kelly",
      "Tokugawa Yoshinobu"
  );

  private static $emails = array(
      "Moriz.Rosenthal@romania.us",
      "Francesco.Orsi@fr.wikipedia.org",
      "Fedele.Fedeli@google.com",
      "Raffaello.Silvestrini@fr.wikipedia.org",
      "Augusto.Murri@microsoft.com",
      "Ligne.de.Damoiseau.Ellis@fr.wikipedia.org",
      "Calvin.Ellis@microsoft.fr",
      "Louis-Hyacinthe-Celeste.Damoiseau@fr.wikipedia.org",
      "Barack.Obama@apple.com",
      "Justin.Trudeau@fr.wikipedia.org",
      "Victor.Ponta@google.fr",
      "Xi.Jinping@google.fr",
      "Ma.Ying-jeou@en.wikipedia.org",
      "Darejan.Dadiani@apple.com",
      "Beaver.Run@es.wikipedia.org",
      "Bowman.Creek@facebook.com",
      "Nelle.Morton@fr.wikipedia.org",
      "Mary.Jane.Kelly@twitter.fr",
      "Tokugawa.Yoshinobu@fr.wikipedia.org"
  );

  /**
   * Création d'un event dans la base de données
   * Utilise quelques données aléatoire plus être le plus rapide possible
   *
   * @return multitype:\ORM\API\PHP\Event boolean
   */

  /**
   * Création d'un event dans la base de données
   * Utilise quelques données aléatoire plus être le plus rapide possible
   *
   * @param int $num Numéro d'itération courant (sert pour la génération des données)
   * @param int $max Numéro max d'itération (sert pour la génération des données)
   * @return mixed
   */
  public static function CreateLightRandomEvent($num, $max) {
    // Génération de l'objet
    $event = new PHP\Event();

    if ($max < 10000) {
      $nb_users = $max / 10;
    } else if ($max < 100000) {
      $nb_users = $max / 100;
    } else {
      $nb_users = $max / 1000;
    }

    $val = $num % $nb_users + $num;
    $time = 1446332400 + (($num % 84500) * 60 * 60) + $val;

    $event->uid = uniqid() . md5(time()) . $num . $val . "@TestORM";
    $event->calendar = self::$users[$val % count(self::$users)] . $val % 10;
    $event->owner = self::$users[$val % count(self::$users)] . $val % 10;
    $event->title = self::$titles[$val % count(self::$titles)];

    $event->created = time();
    $event->modified = time();

    if ($val % 2 === 0) {
      $event->description = self::$descriptions[$val % count(self::$descriptions)];
    }
    $timezone = new \DateTimeZone("Europe/Paris");
    $event->start = new \DateTime("@$time", $timezone);
    $event->start->setTimezone($timezone);
    $event->end = new \DateTime("@" . ($time + 3600 * ($val % 5)), $timezone);
    $event->end->setTimezone($timezone);

    if ($val % 4 === 0) {
      $event->organizer->name = self::$names[$val % count(self::$names)];
      $event->organizer->email = self::$emails[$val % count(self::$emails)];

      $nbatt = $val % 5;
      $attendees = array();
      for ($i = 0; $i < $nbatt; $i++) {
        $attendee = new PHP\Attendee();
        $attendee->name = self::$names[($val + $i * $nbatt) % count(self::$names)];
        $attendee->email = self::$emails[($val + $i * $nbatt) % count(self::$emails)];
        $attendee->response = $attendee::RESPONSE_ACCEPTED;
        $attendee->role = $attendee::ROLE_REQ_PARTICIPANT;
        $attendees[] = $attendee;
      }
      $event->attendees = $attendees;
    }

    if ($val % 5 === 0) {
      $event->categories = array(
          "test1$val",
          "test2$val"
      );
    }

    if ($val % 6 === 0) {
      $event->status = $event::STATUS_TENTATIVE;
      $event->class = $event::CLASS_PRIVATE;
    } else {
      $event->status = $event::STATUS_CONFIRMED;
      $event->class = $event::CLASS_PUBLIC;
    }

    if ($val % 10 === 0) {
      $attachment = new PHP\Attachment();
      $attachment->type = $attachment::TYPE_URL;
      $attachment->data = "https://www.example$val.com/";
      $event->attachments = array(
          $attachment
      );
    } elseif ($val % 11 === 0) {
      $attachment1 = new PHP\Attachment();
      $attachment1->type = $attachment1::TYPE_URL;
      $attachment1->data = "https://www.mysite$val.com/";
      $attachment2 = new PHP\Attachment();
      $attachment2->type = $attachment2::TYPE_URL;
      $attachment2->data = "https://www.othersite$val.com/";
      $event->attachments = array(
          $attachment1,
          $attachment2
      );
    }

    if ($val % 9 === 0) {
      switch ($val % 5) {
        case 0 :
          $event->recurrence->freq = PHP\Recurrence::FREQ_MONTHLY;
          $event->recurrence->count = 5;
          break;
        case 1 :
          $event->recurrence->freq = PHP\Recurrence::FREQ_DAILY;
          $event->recurrence->interval = 2;
          $event->recurrence->until = new \DateTime("@" . ($time + 3600 * 24 * 7));
          break;
        case 2 :
          $event->recurrence->freq = PHP\Recurrence::FREQ_WEEKLY;
          $event->recurrence->byday = PHP\Recurrence::DAY_MONDAY . ',' . PHP\Recurrence::DAY_TUESDAY;
          $event->recurrence->until = new \DateTime("@" . ($time + 3600 * 24 * 7 * 30));
          break;
        case 3 :
          $event->recurrence->freq = PHP\Recurrence::FREQ_YEARLY;
          $event->recurrence->until = new \DateTime("@" . ($time + 3600 * 24 * 7 * 52 * 10));
          break;
        case 4 :
          $event->recurrence->freq = PHP\Recurrence::FREQ_WEEKLY;
          $event->recurrence->interval = 2;
          $event->recurrence->until = new \DateTime("@" . ($time + 3600 * 24 * 7 * 4));
          break;
      }

    }
    $result = $event->insert();
    return array(
        'result' => $result,
        'event' => $event
    );
  }
  /**
   * Lecture d'une liste d'événements dans la base de données
   * Utilise quelques données aléatoire plus être le plus rapide possible
   *
   * @param int $num Numéro d'itération courant (sert pour la génération des données)
   * @param int $max Numéro max d'itération (sert pour la génération des données)
   * @return PHP\Event[]
   */
  public static function ReadRandomEvents($num, $max) {
    // Génération de l'objet
    $event = new PHP\Event();

    if ($max < 10000) {
      $nb_users = $max / 10;
    } else if ($max < 100000) {
      $nb_users = $max / 100;
    } else {
      $nb_users = $max / 1000;
    }

    $val = $num % $nb_users + $num;
    $time = 1446332400 + (($num % 84500) * 60 * 60) + $val;

    if ($val % 6 === 0) {
      $calendar = self::$users[$val % count(self::$users)] . $val % 10;
      $timezone = new \DateTimeZone("Europe/Paris");
      $start = new \DateTime("@$time", $timezone);
      $start->setTimezone($timezone);
      $end = new \DateTime("@" . ($time + (3600*24*30)), $timezone);
      $end->setTimezone($timezone);

      $filter = array(
        Operators::or_0 => array(
          Operators::and_0 => array(
            'calendar' => array(Operators::eq => $calendar),
            'start_0' => array(Operators::gt => $start),
            'start_1' => array(Operators::lt => $end),
          ),
          Operators::and_1 => array(
            'calendar' => array(Operators::eq => $calendar),
            'end_0' => array(Operators::gt => $start),
            'end_1' => array(Operators::lt => $end),
          ),
          Operators::and_2 => array(
            'calendar' => array(Operators::eq => $calendar),
            'recurrence.freq' => array(Operators::neq => null),
            'recurrence.until' => array(Operators::gt => $start),
            'end_1' => array(Operators::lt => $end),
          ),
        ),
      );
      $events = $event->list(null, $filter);
    } else if ($val % 5 === 0) {
      $calendar = self::$users[$val % count(self::$users)] . $val % 10;
      $timezone = new \DateTimeZone("Europe/Paris");
      $start = new \DateTime("@$time", $timezone);
      $start->setTimezone($timezone);
      $end = new \DateTime("@" . ($time + (3600*24)), $timezone);
      $end->setTimezone($timezone);

      $filter = array(
        Operators::or_0 => array(
          Operators::and_0 => array(
            'calendar' => array(Operators::eq => $calendar),
            'start_0' => array(Operators::gt => $start),
            'start_1' => array(Operators::lt => $end),
          ),
          Operators::and_1 => array(
            'calendar' => array(Operators::eq => $calendar),
            'end_0' => array(Operators::gt => $start),
            'end_1' => array(Operators::lt => $end),
          ),
          Operators::and_2 => array(
            'calendar' => array(Operators::eq => $calendar),
            'recurrence.freq' => array(Operators::neq => null),
            'recurrence.until' => array(Operators::gt => $start),
            'end_1' => array(Operators::lt => $end),
          ),
        ),
      );
      $events = $event->list(null, $filter);
    } else {
      $calendar = self::$users[$val % count(self::$users)] . $val % 10;
      $timezone = new \DateTimeZone("Europe/Paris");
      $start = new \DateTime("@$time", $timezone);
      $start->setTimezone($timezone);
      $end = new \DateTime("@" . ($time + (3600*24*7)), $timezone);
      $end->setTimezone($timezone);

      $filter = array(
        Operators::or_0 => array(
          Operators::and_0 => array(
            'calendar' => array(Operators::eq => $calendar),
            'start_0' => array(Operators::gt => $start),
            'start_1' => array(Operators::lt => $end),
          ),
          Operators::and_1 => array(
            'calendar' => array(Operators::eq => $calendar),
            'end_0' => array(Operators::gt => $start),
            'end_1' => array(Operators::lt => $end),
          ),
          Operators::and_2 => array(
            'calendar' => array(Operators::eq => $calendar),
            'recurrence.freq' => array(Operators::neq => null),
            'recurrence.until' => array(Operators::gt => $start),
            'end_1' => array(Operators::lt => $end),
          ),
        ),
      );
      $events = $event->list(null, $filter);
    }
    return $events;
  }
  /**
   * Création d'un event dans la base de données
   * Utilise quelques données aléatoire plus être le plus rapide possible
   *
   * @param int $num Numéro d'itération courant (sert pour la génération des données)
   * @param int $max Numéro max d'itération (sert pour la génération des données)
   * @param string $uid UID de l'événement à modified
   * @param string $calendar Identifiant du calendrier de l'événement à modifier
   * @return boolean
   */
  public static function UpdateRandomEvent($num, $max, $uid, $calendar) {
    // Génération de l'objet
    $event = new PHP\Event();
    $event->uid = $uid;
    $event->calendar = $calendar;
    if ($event->load()) {
      if ($max < 10000) {
        $nb_users = $max / 10;
      } else if ($max < 100000) {
        $nb_users = $max / 100;
      } else {
        $nb_users = $max / 1000;
      }

      $val = $num % $nb_users + $num;
      $time = 1446332400 + (($num % 84500) * 60 * 60) + $val;

      if ($val % 7 === 0) {
        $timezone = new \DateTimeZone("Europe/Paris");
        $event->start = new \DateTime("@$time", $timezone);
        $event->start->setTimezone($timezone);
        $event->end = new \DateTime("@" . ($time + 3600 * ($val % 5)), $timezone);
        $event->end->setTimezone($timezone);

      } else if ($val % 6 === 0) {
        switch ($val % 5) {
          case 0 :
            $event->recurrence->freq = PHP\Recurrence::FREQ_MONTHLY;
            $event->recurrence->count = 5;
            break;
          case 1 :
            $event->recurrence->freq = PHP\Recurrence::FREQ_DAILY;
            $event->recurrence->interval = 2;
            $event->recurrence->until = new \DateTime("@" . ($time + 3600 * 24 * 7));
            break;
          case 2 :
            $event->recurrence->freq = PHP\Recurrence::FREQ_WEEKLY;
            $event->recurrence->byday = PHP\Recurrence::DAY_MONDAY . ',' . PHP\Recurrence::DAY_TUESDAY;
            $event->recurrence->until = new \DateTime("@" . ($time + 3600 * 24 * 7 * 30));
            break;
          case 3 :
            $event->recurrence->freq = PHP\Recurrence::FREQ_YEARLY;
            $event->recurrence->until = new \DateTime("@" . ($time + 3600 * 24 * 7 * 52 * 10));
            break;
          case 4 :
            $event->recurrence->freq = PHP\Recurrence::FREQ_WEEKLY;
            $event->recurrence->interval = 2;
            $event->recurrence->until = new \DateTime("@" . ($time + 3600 * 24 * 7 * 4));
            break;
        }
      } else if ($val % 5 === 0) {
        $nbatt = $val % 5;
        $attendees = array();
        for ($i = 0; $i < $nbatt; $i++) {
          $attendee = new PHP\Attendee();
          $attendee->name = self::$names[($val + $i * $nbatt) % count(self::$names)];
          $attendee->email = self::$emails[($val + $i * $nbatt) % count(self::$emails)];
          $attendee->response = $attendee::RESPONSE_ACCEPTED;
          $attendee->role = $attendee::ROLE_REQ_PARTICIPANT;
          $attendees[] = $attendee;
        }
        $event->attendees = $attendees;

      } else {
        $event->title = self::$titles[$val % count(self::$titles)];
        $event->description = self::$descriptions[($val+1) % count(self::$descriptions)];
      }

      return $event->update();
    }
    else {
      return false;
    }
  }
  /**
   * Suppression d'un événement dans la base de données (pas d'aléatoire)
   * @param string $uid UID de l'événement à modified
   * @param string $calendar Identifiant du calendrier de l'événement à modifier
   * @return boolean
   */
  public static function DeleteEvent($uid, $calendar) {
    // Génération de l'objet
    $event = new PHP\Event();
    $event->uid = $uid;
    $event->calendar = $calendar;
    if ($event->load()) {
      return $event->delete();
    }
    else {
      return false;
    }
  }
}


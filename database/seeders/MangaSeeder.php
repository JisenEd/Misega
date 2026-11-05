<?php

namespace Database\Seeders;

use App\Models\Manga;
use App\Models\Volume;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MangaSeeder extends Seeder
{
    /**
     * Map Jikan API genres to MISEGA's display genre categories
     */
    private function mapGenre(array $item, string $defaultGenre): string
    {
        $genreNames = array_map('strtolower', array_merge(
            array_column($item['genres'] ?? [], 'name'),
            array_column($item['demographics'] ?? [], 'name')
        ));

        $genreString = implode(' ', $genreNames);

        if (in_array('shounen', $genreNames) || str_contains($genreString, 'shounen')) {
            if (str_contains($genreString, 'horror') || (str_contains($genreString, 'action') && str_contains($genreString, 'fantasy'))) {
                return 'Action / Dark Fantasy';
            }
            return 'Shonen';
        }

        if (in_array('seinen', $genreNames) || str_contains($genreString, 'seinen')) {
            if (str_contains($genreString, 'fantasy') || str_contains($genreString, 'horror')) {
                return 'Seinen / Dark Fantasy';
            }
            return 'Seinen';
        }

        if (str_contains($genreString, 'comedy')) {
            return 'Comédie / Action';
        }

        if (str_contains($genreString, 'adventure')) {
            return 'Aventure';
        }

        if (str_contains($genreString, 'fantasy') || str_contains($genreString, 'action')) {
            return 'Action / Dark Fantasy';
        }

        if (str_contains($genreString, 'shoujo')) {
            return 'Shojo';
        }

        return $defaultGenre;
    }

    public function run(): void
    {
        // Admin User
        User::firstOrCreate(
            ['email' => 'admin@misega.fr'],
            [
                'name' => 'Admin Misega',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Client User
        User::firstOrCreate(
            ['email' => 'client@misega.fr'],
            [
                'name' => 'Otaku Fan',
                'password' => Hash::make('password'),
                'role' => 'client',
            ]
        );

        // 50 Real Manga Entries Dataset
        $mangaSeedList = [
            ['title' => 'Jujutsu Kaisen', 'author' => 'Gege Akutami', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/210341l.jpg', 'synopsis' => "Souffrance, regrets, humilation... Les sentiments négatifs que ressentent les humains deviennent des Fléaux qui se terrent dans notre quotidien. Yuji Itadori, un lycéen doté d'une force physique hors du commun, va consommer une relique maudite de classe S pour sauver ses amis."],
            ['title' => 'Chainsaw Man', 'author' => 'Tatsuki Fujimoto', 'genre' => 'Action / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/216464l.jpg', 'synopsis' => "Pour rembourser ses dettes, Denji vit dans la misère la plus totale en chasseur de démons avec Pochita, le démon tronçonneuse. Après une trahison, Denji renait sous la forme d'un hybride démon-humain redoutable."],
            ['title' => 'One Piece', 'author' => 'Eiichiro Oda', 'genre' => 'Aventure', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/253146l.jpg', 'synopsis' => "Monkey D. Luffy rêve de devenir le Roi des Pirates en trouvant le légendaire trésor laissé par Gol D. Roger : le One Piece. Entouré de son équipage au Chapeau de Paille, il sillonne les mers du monde entier."],
            ['title' => 'Berserk', 'author' => 'Kentarou Miura', 'genre' => 'Seinen / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/1/157897l.jpg', 'synopsis' => "Guts, le Guerrier Noir, promène son imposante silhouette et son épée gigantesque dans un monde médiéval fantastique ravagé par les guerres et les démons. Une quête de vengeance épique et sombre."],
            ['title' => 'Spy x Family', 'author' => 'Tatsuya Endo', 'genre' => 'Comédie / Action', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/219741l.jpg', 'synopsis' => "Twilight, un espion d'élite, doit fonder une famille factice pour une mission ultra-secrète. Ce qu'il ne sait pas, c'est que sa femme est une tueuse à gages et sa fille adoptive une télépathe !"],
            ['title' => 'Demon Slayer', 'author' => 'Koyoharu Gotouge', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/179023l.jpg', 'synopsis' => "Tanjiro, un jeune vendeur de charbon, voit sa famille massacrée par un démon. Sa sœur Nezuko est la seule survivante, mais elle est transformée en démon. Tanjiro s'engage chez les Pourfendeurs de Démons pour la sauver."],
            ['title' => 'Attack on Titan', 'author' => 'Hajime Isayama', 'genre' => 'Action / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/37846l.jpg', 'synopsis' => "L'humanité vit enfermée dans des cités entourées d'immenses murs pour se protéger des Titans, de gigantesques créatures humanoïdes dévoreuses d'hommes."],
            ['title' => 'My Hero Academia', 'author' => 'Kohei Horikoshi', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/1/157338l.jpg', 'synopsis' => "Dans un monde où 80% de la population possède un super-pouvoir appelé « Alter », Izuku Midoriya est un des rares nés sans aucun pouvoir. Pourtant, il rêve d'intégrer la prestigieuse académie d'héros."],
            ['title' => 'Naruto', 'author' => 'Masashi Kishimoto', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/117681l.jpg', 'synopsis' => "Naruto Uzumaki est un jeune ninja marginal rejeté par les habitants de son village parce qu'il abrite en lui le Démon Renard à Neuf Queues. Son ambition : devenir Hokage."],
            ['title' => 'Bleach', 'author' => 'Tite Kubo', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/180031l.jpg', 'synopsis' => "Ichigo Kurosaki, un lycéen capable de voir les fantômes, rencontre Rukia Kuchiki, un Shinigami. Lorsqu'elle est blessée, elle lui transfère ses pouvoirs pour éliminer un Hollow."],
            ['title' => 'Dragon Ball', 'author' => 'Akira Toriyama', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/54453l.jpg', 'synopsis' => "Son Goku, un jeune garçon doté d'une queue de singe et d'une force surhumaine, part à la recherche des 7 Dragon Balls capables d'exaucer n'importe quel vœu."],
            ['title' => 'Hunter x Hunter', 'author' => 'Yoshihiro Togashi', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/253151l.jpg', 'synopsis' => "Gon Freecss rêve de devenir un Hunter pour retrouver son père Ging. En chemin pour passer l'examen redoutable, il se lie d'amitié avec Killua, Kurapika et Leorio."],
            ['title' => 'Death Note', 'author' => 'Tsugumi Ohba', 'genre' => 'Seinen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/253147l.jpg', 'synopsis' => "Light Yagami trouve le Death Note, un cahier magique laissé par un dieu de la mort. Tout humain dont le nom est inscrit dans ce cahier meurt. Light décide de purifier le monde."],
            ['title' => 'Tokyo Ghoul', 'author' => 'Sui Ishida', 'genre' => 'Action / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/164947l.jpg', 'synopsis' => "À Tokyo, des goules se mêlent aux humains pour se nourrir de leur chair. Ken Kaneki devient un hybride mi-humain mi-goule après une greffe d'organes tragique."],
            ['title' => 'Fullmetal Alchemist', 'author' => 'Hiromu Arakawa', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/243675l.jpg', 'synopsis' => "Edward et Alphonse Elric ont tenté de ressusciter leur mère grâce à l'alchimie, brisant le tabou ultime. Edward perd une jambe et un bras, Alphonse son corps entier."],
            ['title' => 'Vinland Saga', 'author' => 'Makoto Yukimura', 'genre' => 'Seinen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/188177l.jpg', 'synopsis' => "Thorfinn, fils d'un légendaire guerrier viking, grandit au milieu de mercenaires sanguinaires pour accomplir sa vengeance contre Askeladd, l'assassin de son père."],
            ['title' => 'Slam Dunk', 'author' => 'Takehiko Inoue', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/258749l.jpg', 'synopsis' => "Hanamichi Sakuragi, un voyou de lycéen rejeté par 50 filles, s'inscrit au club de basket pour séduire la passionnée Haruko. Il se découvre un talent brut impressionnant."],
            ['title' => 'Monster', 'author' => 'Naoki Urasawa', 'genre' => 'Seinen', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/258224l.jpg', 'synopsis' => "Le neurochirurgien Kenzo Tenma sauve la vie d'un jeune garçon blessé par balle plutôt que celle du maire. Des années plus tard, ce garçon est devenu un monstre froid et sociopathe."],
            ['title' => 'Vagabond', 'author' => 'Takehiko Inoue', 'genre' => 'Seinen', 'cover' => 'https://cdn.myanimelist.net/images/manga/1/259287l.jpg', 'synopsis' => "Adaptation de la vie de Miyamoto Musashi, le plus grand escrimeur du Japon féodal, dans sa quête d'illumination par le sabre."],
            ['title' => 'Kingdom', 'author' => 'Yasuhisa Hara', 'genre' => 'Seinen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/171872l.jpg', 'synopsis' => "Dans la Chine ancienne de la période des Royaumes combattants, Shin, un orphelin de guerre, ambitionne de devenir le plus grand général sous les cieux."],
            ['title' => 'JoJo no Kimyou na Bouken', 'author' => 'Hirohiko Araki', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/54583l.jpg', 'synopsis' => "La saga de la famille Joestar à travers les générations, luttant contre le masque de pierre et les utilisateurs de Stands mystiques."],
            ['title' => 'Haikyuu!!', 'author' => 'Haruichi Furudate', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/182513l.jpg', 'synopsis' => "Shoyo Hinata, malgré sa petite taille, est passionné de volley-ball. Il rejoint le club du lycée Karasuno et fait équipe avec son rival Tobio Kageyama."],
            ['title' => 'Solo Leveling', 'author' => 'Chugong', 'genre' => 'Action / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/222240l.jpg', 'synopsis' => "Sung Jinwoo, le chasseur le plus faible du monde, obtient un système de quêtes mystérieux qui lui permet de monter de niveau sans aucune limite."],
            ['title' => 'Blue Lock', 'author' => 'Muneyuki Kaneshiro', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/217342l.jpg', 'synopsis' => "300 attaquants lycéens sont enfermés dans le centre d'entraînement Blue Lock pour former l'attaquant ultime et le plus égoïste de l'équipe du Japon."],
            ['title' => 'Gintama', 'author' => 'Hideaki Sorachi', 'genre' => 'Comédie / Action', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/166046l.jpg', 'synopsis' => "Dans un Edo envahi par des extraterrestres Amanto, le samouraï excentrique Gintoki Sakata fait n'importe quel travail pour payer son loyer."],
            ['title' => 'One Punch Man', 'author' => 'ONE', 'genre' => 'Comédie / Action', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/258226l.jpg', 'synopsis' => "Saitama est un héros qui terrasse tous ses ennemis d'un seul coup de poing. Tellement puissant qu'il s'ennuie fermement et cherche un adversaire à sa hauteur."],
            ['title' => 'Mob Psycho 100', 'author' => 'ONE', 'genre' => 'Comédie / Action', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/171804l.jpg', 'synopsis' => "Shigeo Kageyama, surnommé Mob, est un collégien doté d'immenses pouvoirs psychiques. Il tente de réprimer ses émotions pour mener une vie normale."],
            ['title' => 'Tokyo Revengers', 'author' => 'Ken Wakui', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/206126l.jpg', 'synopsis' => "Takemichi Hanagaki remonte 12 ans dans le passé pour sauver son ancienne petite amie d'un gang de voyous de Tokyo."],
            ['title' => 'Dr. Stone', 'author' => 'Riichiro Inagaki', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/194481l.jpg', 'synopsis' => "Toute l'humanité est pétrifiée par un rayon mystérieux. Des milliers d'années plus tard, Senku, un génie de la science, se réveille et décide de rebâtir la civilisation."],
            ['title' => 'Black Clover', 'author' => 'Yuki Tabata', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/166245l.jpg', 'synopsis' => "Asta, né sans une goutte de magie dans un monde où tout le monde en possède, obtient un grimoire de l'anti-magie à 5 trèfles pour devenir Empereur-Mage."],
            ['title' => 'Fairy Tail', 'author' => 'Hiro Mashima', 'genre' => 'Aventure', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/181671l.jpg', 'synopsis' => "Lucy Heartfilia rejoint la guilde magique déjantée Fairy Tail et fait équipe avec Natsu Dragneel, le chasseur de dragons de feu."],
            ['title' => 'Fire Force', 'author' => 'Atsushi Ohkubo', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/178051l.jpg', 'synopsis' => "Des humains se transforment spontanément en Torches Humaines destructrices. La 8e brigade spéciale des pompiers lutte pour éteindre ces brasiers."],
            ['title' => 'Kaiju No. 8', 'author' => 'Naoya Matsumoto', 'genre' => 'Action / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/237077l.jpg', 'synopsis' => "Kafka Hibino, nettoyeur de cadavres de monstres, absorbe un organisme Kaiju et acquiert la capacité de se transformer en un monstre humanoïde puissant."],
            ['title' => 'Soul Eater', 'author' => 'Atsushi Ohkubo', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/258752l.jpg', 'synopsis' => "Les faucheurs d'âmes et leurs armes métamorphes de l'académie Shibusen récoltent 99 âmes humaines corrompues et 1 âme de sorcière pour devenir la Death Scythe."],
            ['title' => 'Noragami', 'author' => 'Adachitoka', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/180718l.jpg', 'synopsis' => "Yato est un dieu mineur vagabond sans sanctuaire qui accomplit des petits boulots pour 5 yens, rêvant de devenir le dieu le plus vénéré du Japon."],
            ['title' => 'Blue Exorcist', 'author' => 'Kazue Kato', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/181488l.jpg', 'synopsis' => "Rin Okumura découvre qu'il est le fils de Satan. Pour se venger de la mort de son père adoptif, il décide de devenir exorciste à l'académie de la Croix-Vraie."],
            ['title' => 'Seven Deadly Sins', 'author' => 'Nakaba Suzuki', 'genre' => 'Aventure', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/178050l.jpg', 'synopsis' => "La princesse Elizabeth recherche les Seven Deadly Sins, sept chevaliers légendaires déclarés traîtres, pour libérer le royaume des Chevaliers Sacrés."],
            ['title' => 'Made in Abyss', 'author' => 'Akihito Tsukushi', 'genre' => 'Aventure', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/182512l.jpg', 'synopsis' => "Riko explore l'Abysse, une immense cavité inexplorée remplie de reliques et de monstres féroces, accompagnée du robot amnésique Reg."],
            ['title' => 'The Promised Neverland', 'author' => 'Kaiu Shirai', 'genre' => 'Action / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/188176l.jpg', 'synopsis' => "Emma, Norman et Ray vivent dans l'orphelinat Grace Field House. Ils découvrent la vérité terrifiante : ils sont élevés comme nourriture pour des démons."],
            ['title' => 'Kaguya-sama: Love is War', 'author' => 'Aka Akasaka', 'genre' => 'Comédie / Action', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/188883l.jpg', 'synopsis' => "Kaguya Shinomiya et Miyuki Shirogane sont deux génies du BDE amoureux l'un de l'autre, mais trop orgueilleux pour l'avouer. C'est la guerre psychologique !"],
            ['title' => 'Food Wars!', 'author' => 'Yuto Tsukuda', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/181489l.jpg', 'synopsis' => "Soma Yukihira intègre la prestigieuse école culinaire Totsuki où les duels de cuisine (Shokugeki) décident du destin des élèves."],
            ['title' => 'Assassination Classroom', 'author' => 'Yusei Matsui', 'genre' => 'Comédie / Action', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/178052l.jpg', 'synopsis' => "Une pieuvre extraterrestre indestructible qui a détruit 70% de la Lune devient le professeur de la classe 3-E. Les élèves doivent l'assassiner avant la fin de l'année."],
            ['title' => 'Parasyte', 'author' => 'Hitoshi Iwaaki', 'genre' => 'Seinen / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/258750l.jpg', 'synopsis' => "Des parasites extraterrestres s'infiltrent dans les cerveaux humains. Shinichi Izumi réussit à empêcher le sien de monter à la tête : il fusionne avec sa main droite."],
            ['title' => 'Claymore', 'author' => 'Norihiro Yagi', 'genre' => 'Action / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/258751l.jpg', 'synopsis' => "Dans un monde ravagé par les Yomas, des guerrières hybrides mi-humaines mi-démons connues sous le nom de Claymores sont engagées pour les éliminer."],
            ['title' => 'Hell\'s Paradise', 'author' => 'Yuji Kaku', 'genre' => 'Action / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/213506l.jpg', 'synopsis' => "Gabimaru le Sans-Émotion, un ninja condamné à mort, se voit offrir le pardon s'il rapporte l'Élixir d'Immortalité d'une île paradisiaque cauchemardesque."],
            ['title' => 'Dandadan', 'author' => 'Yukinobu Tatsu', 'genre' => 'Action / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/250787l.jpg', 'synopsis' => "Momo Ayase croit aux fantômes mais pas aux aliens, Ken Takakura croit aux aliens mais pas aux fantômes. En explorant des lieux hantés, ils découvrent que les deux existent."],
            ['title' => 'Sakamoto Days', 'author' => 'Yuto Suzuki', 'genre' => 'Comédie / Action', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/243676l.jpg', 'synopsis' => "Taro Sakamoto, le plus grand tueur à gages légendaire, a pris sa retraite après être tombé amoureux. Désormais épicier en surpoids, il doit défendre sa famille."],
            ['title' => 'Frieren: Beyond Journey\'s End', 'author' => 'Kanehito Yamada', 'genre' => 'Aventure', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/232470l.jpg', 'synopsis' => "Après la défaite du Roi Démon, l'elfe Frieren aux millénaires de vie voit ses compagnons humains vieillir et mourir. Elle entreprend un voyage pour mieux comprendre l'humanité."],
            ['title' => 'Boruto: Two Blue Vortex', 'author' => 'Masashi Kishimoto', 'genre' => 'Shonen', 'cover' => 'https://cdn.myanimelist.net/images/manga/3/287312l.jpg', 'synopsis' => "Trois ans après la réécriture de la réalité par Omnipotence, Boruto Uzumaki revient plus fort que jamais comme fugitif poursuivi par Konoha."],
            ['title' => 'D.Gray-man', 'author' => 'Katsura Hoshino', 'genre' => 'Action / Dark Fantasy', 'cover' => 'https://cdn.myanimelist.net/images/manga/2/258753l.jpg', 'synopsis' => "Allen Walker est un jeune exorciste de la Congrégation de l'Ombre doté d'un œil maudit et d'une Innocence parasite pour combattre le Comte Millénaire et ses Akuma."],
        ];

        $seededCount = 0;

        foreach ($mangaSeedList as $item) {
            $title = $item['title'];
            $cleanTitle = explode(' (', $title)[0];
            $cleanTitle = explode(':', $cleanTitle)[0];

            $coverUrl = $item['cover'];
            $synopsis = $item['synopsis'];
            $author = $item['author'];
            $genre = $item['genre'];
            $status = 'En cours';
            $volumesCount = rand(4, 12);

            // Attempt to enrich with live Jikan API data if available
            try {
                $url = 'https://api.jikan.moe/v4/manga?q=' . urlencode($cleanTitle) . '&limit=1';
                $res = Http::withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'])
                    ->withoutVerifying()
                    ->timeout(4)
                    ->get($url);

                if ($res->successful()) {
                    $json = json_decode($res->body(), true);
                    if (isset($json['data'][0])) {
                        $apiData = $json['data'][0];

                        // Extract live cover if valid
                        if (!empty($apiData['images']['jpg']['large_image_url'])) {
                            $coverUrl = $apiData['images']['jpg']['large_image_url'];
                        }

                        // Extract live author
                        if (!empty($apiData['authors'][0]['name'])) {
                            $apiAuthor = $apiData['authors'][0]['name'];
                            if (str_contains($apiAuthor, ', ')) {
                                $parts = explode(', ', $apiAuthor, 2);
                                $apiAuthor = $parts[1] . ' ' . $parts[0];
                            }
                            $author = $apiAuthor;
                        }

                        // Extract live status
                        if (isset($apiData['status'])) {
                            $status = $apiData['status'] === 'Finished' ? 'Terminé' : 'En cours';
                        }

                        // Extract live volumes count
                        if (isset($apiData['volumes']) && (int)$apiData['volumes'] > 0) {
                            $volumesCount = (int)$apiData['volumes'];
                            if ($volumesCount > 15) $volumesCount = 10;
                        }

                        // Extract live synopsis
                        if (!empty($apiData['synopsis'])) {
                            $syn = preg_replace('/\[Written by MAL Rewrite\]/', '', $apiData['synopsis']);
                            $syn = trim($syn);
                            if (strlen($syn) > 20) {
                                $synopsis = $syn;
                            }
                        }

                        // Extract mapped genre
                        $genre = $this->mapGenre($apiData, $genre);
                    }
                }
            } catch (\Exception $e) {
                Log::info("Jikan fetch note for '{$title}': " . $e->getMessage());
            }

            // Random realistic price in FCFA between 4200 and 6500 FCFA (rounded to nearest 50 FCFA)
            $price = rand(84, 130) * 50;

            $manga = Manga::updateOrCreate(
                ['title' => $title],
                [
                    'title' => $title,
                    'author' => $author,
                    'publisher' => 'Éditions Misega',
                    'genre' => $genre,
                    'synopsis' => $synopsis,
                    'status' => $status,
                    'cover_url' => null,
                    'cover_image' => null,
                    'is_featured' => $seededCount < 6, // First 6 are featured
                ]
            );

            for ($i = 1; $i <= $volumesCount; $i++) {
                Volume::updateOrCreate(
                    [
                        'manga_id' => $manga->id,
                        'volume_number' => $i,
                    ],
                    [
                        'price' => $price,
                        'stock' => rand(5, 30),
                        'cover_image_url' => null,
                        'isbn' => '978-2-' . rand(10000, 99999) . '-' . rand(100, 999) . '-' . rand(0, 9),
                    ]
                );
            }

            $seededCount++;

            // Respect Jikan API rate limits (300ms delay)
            usleep(300000);
        }
    }
}

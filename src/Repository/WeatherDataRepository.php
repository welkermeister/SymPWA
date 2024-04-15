<?php

namespace App\Repository;

use App\Entity\WeatherData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<WeatherData>
 *
 * @method WeatherData|null find($id, $lockMode = null, $lockVersion = null)
 * @method WeatherData|null findOneBy(array $criteria, array $orderBy = null)
 * @method WeatherData[]    findAll()
 * @method WeatherData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeatherDataRepository extends ServiceEntityRepository
{

    private UserRepository $userRepository;

    public function __construct(ManagerRegistry $registry, UserRepository $userRepository)
    {
        parent::__construct($registry, WeatherData::class);
        $this->userRepository = $userRepository;
    }

//    /**
//     * @return WeatherData[] Returns an array of WeatherData objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WeatherData
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function createWeatherData(UserInterface $user, array $weather): void
    {
        $newWeatherData = new WeatherData();
        $newWeatherData->setLatitude($weather["coord"]["lat"]);
        $newWeatherData->setLongitude($weather["coord"]["lon"]);
        $newWeatherData->setCity($weather["name"]);
        $newWeatherData->setUser($user);
        $newWeatherData->setTemperature($weather["main"]["temp"]);
        $newWeatherData->setFeelsLike($weather["main"]["feels_like"]);
        $newWeatherData->setDescription($weather["weather"][0]["description"]);

        $this->_em->persist($newWeatherData);
        $this->_em->flush();
    }

    public function updateWeatherData(array $weather, WeatherData $weatherData): void
    {
        $weatherData->setTemperature($weather["main"]["temp"]);
        $weatherData->setFeelsLike($weather["main"]["feels_like"]);
        $weatherData->setDescription($weather["weather"][0]["description"]);

        $this->_em->persist($weatherData);
        $this->_em->flush();
    }

    public function upsertWeatherData(UserInterface $user, array $weather): void
    {
        $weatherData = $this->findByUserAndCity($user, $weather["name"]);
        if($weatherData) {
            $this->updateWeatherData($weather, $weatherData);
        }
        else {
            $this->createWeatherData($user, $weather);
        }
    }

    public function findByUserAndCity(UserInterface $user, string $city): ? WeatherData
    {
        $userIdentifier = $user->getUserIdentifier();
        $userId = $this->userRepository->findByEmail($userIdentifier)[0]->getId();

        return $this->createQueryBuilder("w")
        ->where("w.user = :user")
        ->andWhere("w.city = :city")
        ->setParameters(new ArrayCollection(array(
            new Parameter('user', $userId), 
            new Parameter('city', $city)
            )))
        ->getQuery()
        ->getOneOrNullResult();
    }
}

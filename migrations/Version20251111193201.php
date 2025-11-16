<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251111193201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE producto_variacion (id INT AUTO_INCREMENT NOT NULL, producto_id INT NOT NULL, talla VARCHAR(50) NOT NULL, color VARCHAR(50) NOT NULL, stock INT NOT NULL, precio NUMERIC(10, 2) DEFAULT NULL, imagen VARCHAR(255) DEFAULT NULL, INDEX IDX_59C4E9E67645698E (producto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE producto_variacion ADD CONSTRAINT FK_59C4E9E67645698E FOREIGN KEY (producto_id) REFERENCES productos (id)');
        $this->addSql('ALTER TABLE carrito RENAME INDEX fk_carrito_usuario TO IDX_77E6BED5DB38439E');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY fk_carrito_producto_carrito');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY fk_carrito_producto_producto');
        $this->addSql('ALTER TABLE carrito_producto CHANGE carrito_id carrito_id INT DEFAULT NULL, CHANGE producto_id producto_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT FK_62C02DC2DE2CF6E7 FOREIGN KEY (carrito_id) REFERENCES carrito (id)');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT FK_62C02DC27645698E FOREIGN KEY (producto_id) REFERENCES productos (id)');
        $this->addSql('ALTER TABLE carrito_producto RENAME INDEX fk_carrito_producto_carrito TO IDX_62C02DC2DE2CF6E7');
        $this->addSql('ALTER TABLE carrito_producto RENAME INDEX fk_carrito_producto_producto TO IDX_62C02DC27645698E');
        $this->addSql('ALTER TABLE categoria CHANGE descripcion descripcion LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY fk_comentario_usuario');
        $this->addSql('ALTER TABLE comentario CHANGE texto texto LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT FK_4B91E702DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id)');
        $this->addSql('ALTER TABLE comentario RENAME INDEX fk_comentario_usuario TO IDX_4B91E702DB38439E');
        $this->addSql('ALTER TABLE comentario RENAME INDEX fk_comentario_producto TO IDX_4B91E7027645698E');
        $this->addSql('ALTER TABLE direccion DROP FOREIGN KEY fk_direccion_usuario');
        $this->addSql('ALTER TABLE direccion CHANGE usuario_id usuario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE direccion ADD CONSTRAINT FK_F384BE95DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id)');
        $this->addSql('ALTER TABLE direccion RENAME INDEX fk_direccion_usuario TO IDX_F384BE95DB38439E');
        $this->addSql('ALTER TABLE metodo_pago CHANGE proveedor proveedor VARCHAR(50) DEFAULT NULL, CHANGE datos datos VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY fk_pedido_producto_pedido');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY fk_pedido_producto_producto');
        $this->addSql('ALTER TABLE pedido_producto CHANGE pedido_id pedido_id INT DEFAULT NULL, CHANGE producto_id producto_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT FK_DD333C24854653A FOREIGN KEY (pedido_id) REFERENCES pedidos (id)');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT FK_DD333C27645698E FOREIGN KEY (producto_id) REFERENCES productos (id)');
        $this->addSql('ALTER TABLE pedido_producto RENAME INDEX fk_pedido_producto_pedido TO IDX_DD333C24854653A');
        $this->addSql('ALTER TABLE pedido_producto RENAME INDEX fk_pedido_producto_producto TO IDX_DD333C27645698E');
        $this->addSql('ALTER TABLE pedidos RENAME INDEX fk_pedidos_usuario TO IDX_6716CCAADB38439E');
        $this->addSql('ALTER TABLE pedidos RENAME INDEX fk_pedidos_direccion TO IDX_6716CCAAD0A7BD7');
        $this->addSql('ALTER TABLE pedidos RENAME INDEX fk_pedidos_metodo_pago TO IDX_6716CCAA34676066');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY fk_productos_categoria');
        $this->addSql('ALTER TABLE productos CHANGE descripcion descripcion LONGTEXT DEFAULT NULL, CHANGE imagen imagen VARCHAR(255) DEFAULT NULL, CHANGE talla talla VARCHAR(10) DEFAULT NULL, CHANGE color color VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT FK_767490E63397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('ALTER TABLE productos RENAME INDEX fk_productos_categoria TO IDX_767490E63397707A');
        $this->addSql('ALTER TABLE usuarios ADD roles JSON NOT NULL, CHANGE contraseña password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE usuarios RENAME INDEX email TO UNIQ_EF687F2E7927C74');
        $this->addSql('ALTER TABLE usuarios RENAME INDEX telefono TO UNIQ_EF687F2C1E70A7F');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE producto_variacion DROP FOREIGN KEY FK_59C4E9E67645698E');
        $this->addSql('DROP TABLE producto_variacion');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE carrito RENAME INDEX idx_77e6bed5db38439e TO fk_carrito_usuario');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY FK_62C02DC2DE2CF6E7');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY FK_62C02DC27645698E');
        $this->addSql('ALTER TABLE carrito_producto CHANGE carrito_id carrito_id INT NOT NULL, CHANGE producto_id producto_id INT NOT NULL');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT fk_carrito_producto_carrito FOREIGN KEY (carrito_id) REFERENCES carrito (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT fk_carrito_producto_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrito_producto RENAME INDEX idx_62c02dc27645698e TO fk_carrito_producto_producto');
        $this->addSql('ALTER TABLE carrito_producto RENAME INDEX idx_62c02dc2de2cf6e7 TO fk_carrito_producto_carrito');
        $this->addSql('ALTER TABLE categoria CHANGE descripcion descripcion TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY FK_4B91E702DB38439E');
        $this->addSql('ALTER TABLE comentario CHANGE texto texto TEXT NOT NULL');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT fk_comentario_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comentario RENAME INDEX idx_4b91e702db38439e TO fk_comentario_usuario');
        $this->addSql('ALTER TABLE comentario RENAME INDEX idx_4b91e7027645698e TO fk_comentario_producto');
        $this->addSql('ALTER TABLE direccion DROP FOREIGN KEY FK_F384BE95DB38439E');
        $this->addSql('ALTER TABLE direccion CHANGE usuario_id usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE direccion ADD CONSTRAINT fk_direccion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE direccion RENAME INDEX idx_f384be95db38439e TO fk_direccion_usuario');
        $this->addSql('ALTER TABLE metodo_pago CHANGE proveedor proveedor VARCHAR(50) DEFAULT \'NULL\', CHANGE datos datos VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE pedidos RENAME INDEX idx_6716ccaadb38439e TO fk_pedidos_usuario');
        $this->addSql('ALTER TABLE pedidos RENAME INDEX idx_6716ccaad0a7bd7 TO fk_pedidos_direccion');
        $this->addSql('ALTER TABLE pedidos RENAME INDEX idx_6716ccaa34676066 TO fk_pedidos_metodo_pago');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY FK_DD333C24854653A');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY FK_DD333C27645698E');
        $this->addSql('ALTER TABLE pedido_producto CHANGE pedido_id pedido_id INT NOT NULL, CHANGE producto_id producto_id INT NOT NULL');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT fk_pedido_producto_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT fk_pedido_producto_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedido_producto RENAME INDEX idx_dd333c24854653a TO fk_pedido_producto_pedido');
        $this->addSql('ALTER TABLE pedido_producto RENAME INDEX idx_dd333c27645698e TO fk_pedido_producto_producto');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY FK_767490E63397707A');
        $this->addSql('ALTER TABLE productos CHANGE descripcion descripcion TEXT DEFAULT NULL, CHANGE imagen imagen VARCHAR(255) DEFAULT \'NULL\', CHANGE talla talla VARCHAR(10) DEFAULT \'NULL\', CHANGE color color VARCHAR(30) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT fk_productos_categoria FOREIGN KEY (categoria_id) REFERENCES categoria (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE productos RENAME INDEX idx_767490e63397707a TO fk_productos_categoria');
        $this->addSql('ALTER TABLE usuarios DROP roles, CHANGE password contraseña VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE usuarios RENAME INDEX uniq_ef687f2c1e70a7f TO telefono');
        $this->addSql('ALTER TABLE usuarios RENAME INDEX uniq_ef687f2e7927c74 TO email');
    }
}

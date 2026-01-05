<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260105131617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carrito DROP FOREIGN KEY fk_carrito_usuario');
        $this->addSql('DROP INDEX fk_carrito_usuario ON carrito');
        $this->addSql('CREATE INDEX IDX_77E6BED5DB38439E ON carrito (usuario_id)');
        $this->addSql('ALTER TABLE carrito ADD CONSTRAINT fk_carrito_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY fk_carrito_producto_carrito');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY fk_carrito_producto_producto');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY fk_carrito_producto_carrito');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY fk_carrito_producto_producto');
        $this->addSql('ALTER TABLE carrito_producto CHANGE carrito_id carrito_id INT DEFAULT NULL, CHANGE producto_id producto_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT FK_62C02DC2DE2CF6E7 FOREIGN KEY (carrito_id) REFERENCES carrito (id)');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT FK_62C02DC27645698E FOREIGN KEY (producto_id) REFERENCES productos (id)');
        $this->addSql('DROP INDEX fk_carrito_producto_carrito ON carrito_producto');
        $this->addSql('CREATE INDEX IDX_62C02DC2DE2CF6E7 ON carrito_producto (carrito_id)');
        $this->addSql('DROP INDEX fk_carrito_producto_producto ON carrito_producto');
        $this->addSql('CREATE INDEX IDX_62C02DC27645698E ON carrito_producto (producto_id)');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT fk_carrito_producto_carrito FOREIGN KEY (carrito_id) REFERENCES carrito (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT fk_carrito_producto_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categoria CHANGE descripcion descripcion LONGTEXT DEFAULT NULL');
        $this->addSql('DROP INDEX slug ON categoria');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E10122D989D9B62 ON categoria (slug)');
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY fk_comentario_usuario');
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY fk_comentario_usuario');
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY fk_comentario_producto');
        $this->addSql('ALTER TABLE comentario CHANGE texto texto LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT FK_4B91E702DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id)');
        $this->addSql('DROP INDEX fk_comentario_usuario ON comentario');
        $this->addSql('CREATE INDEX IDX_4B91E702DB38439E ON comentario (usuario_id)');
        $this->addSql('DROP INDEX fk_comentario_producto ON comentario');
        $this->addSql('CREATE INDEX IDX_4B91E7027645698E ON comentario (producto_id)');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT fk_comentario_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT fk_comentario_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE direccion DROP FOREIGN KEY fk_direccion_usuario');
        $this->addSql('ALTER TABLE direccion DROP FOREIGN KEY fk_direccion_usuario');
        $this->addSql('ALTER TABLE direccion CHANGE usuario_id usuario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE direccion ADD CONSTRAINT FK_F384BE95DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id)');
        $this->addSql('DROP INDEX fk_direccion_usuario ON direccion');
        $this->addSql('CREATE INDEX IDX_F384BE95DB38439E ON direccion (usuario_id)');
        $this->addSql('ALTER TABLE direccion ADD CONSTRAINT fk_direccion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lineas_pedido DROP FOREIGN KEY fk_linea_pedido_producto');
        $this->addSql('ALTER TABLE lineas_pedido DROP FOREIGN KEY fk_linea_pedido_pedido');
        $this->addSql('DROP INDEX fk_linea_pedido_pedido ON lineas_pedido');
        $this->addSql('CREATE INDEX IDX_D2DE2C134854653A ON lineas_pedido (pedido_id)');
        $this->addSql('DROP INDEX fk_linea_pedido_producto ON lineas_pedido');
        $this->addSql('CREATE INDEX IDX_D2DE2C137645698E ON lineas_pedido (producto_id)');
        $this->addSql('ALTER TABLE lineas_pedido ADD CONSTRAINT fk_linea_pedido_producto FOREIGN KEY (producto_id) REFERENCES productos (id)');
        $this->addSql('ALTER TABLE lineas_pedido ADD CONSTRAINT fk_linea_pedido_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY fk_pedido_producto_pedido');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY fk_pedido_producto_producto');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY fk_pedido_producto_pedido');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY fk_pedido_producto_producto');
        $this->addSql('ALTER TABLE pedido_producto CHANGE pedido_id pedido_id INT DEFAULT NULL, CHANGE producto_id producto_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT FK_DD333C24854653A FOREIGN KEY (pedido_id) REFERENCES pedidos (id)');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT FK_DD333C27645698E FOREIGN KEY (producto_id) REFERENCES productos (id)');
        $this->addSql('DROP INDEX fk_pedido_producto_pedido ON pedido_producto');
        $this->addSql('CREATE INDEX IDX_DD333C24854653A ON pedido_producto (pedido_id)');
        $this->addSql('DROP INDEX fk_pedido_producto_producto ON pedido_producto');
        $this->addSql('CREATE INDEX IDX_DD333C27645698E ON pedido_producto (producto_id)');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT fk_pedido_producto_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT fk_pedido_producto_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY fk_pedidos_usuario');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY fk_pedidos_direccion');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY fk_pedidos_metodo_pago');
        $this->addSql('DROP INDEX fk_pedidos_usuario ON pedidos');
        $this->addSql('CREATE INDEX IDX_6716CCAADB38439E ON pedidos (usuario_id)');
        $this->addSql('DROP INDEX fk_pedidos_direccion ON pedidos');
        $this->addSql('CREATE INDEX IDX_6716CCAAD0A7BD7 ON pedidos (direccion_id)');
        $this->addSql('DROP INDEX fk_pedidos_metodo_pago ON pedidos');
        $this->addSql('CREATE INDEX IDX_6716CCAA34676066 ON pedidos (metodo_pago_id)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT fk_pedidos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT fk_pedidos_direccion FOREIGN KEY (direccion_id) REFERENCES direccion (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT fk_pedidos_metodo_pago FOREIGN KEY (metodo_pago_id) REFERENCES metodo_pago (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY fk_productos_categoria');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY fk_productos_categoria');
        $this->addSql('ALTER TABLE productos CHANGE descripcion descripcion LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT FK_767490E63397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('DROP INDEX fk_productos_categoria ON productos');
        $this->addSql('CREATE INDEX IDX_767490E63397707A ON productos (categoria_id)');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT fk_productos_categoria FOREIGN KEY (categoria_id) REFERENCES categoria (id) ON DELETE SET NULL');
        $this->addSql('DROP INDEX email ON usuarios');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EF687F2E7927C74 ON usuarios (email)');
        $this->addSql('DROP INDEX telefono ON usuarios');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EF687F2C1E70A7F ON usuarios (telefono)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carrito DROP FOREIGN KEY FK_77E6BED5DB38439E');
        $this->addSql('DROP INDEX idx_77e6bed5db38439e ON carrito');
        $this->addSql('CREATE INDEX fk_carrito_usuario ON carrito (usuario_id)');
        $this->addSql('ALTER TABLE carrito ADD CONSTRAINT FK_77E6BED5DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY FK_62C02DC2DE2CF6E7');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY FK_62C02DC27645698E');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY FK_62C02DC2DE2CF6E7');
        $this->addSql('ALTER TABLE carrito_producto DROP FOREIGN KEY FK_62C02DC27645698E');
        $this->addSql('ALTER TABLE carrito_producto CHANGE carrito_id carrito_id INT NOT NULL, CHANGE producto_id producto_id INT NOT NULL');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT fk_carrito_producto_carrito FOREIGN KEY (carrito_id) REFERENCES carrito (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT fk_carrito_producto_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX idx_62c02dc2de2cf6e7 ON carrito_producto');
        $this->addSql('CREATE INDEX fk_carrito_producto_carrito ON carrito_producto (carrito_id)');
        $this->addSql('DROP INDEX idx_62c02dc27645698e ON carrito_producto');
        $this->addSql('CREATE INDEX fk_carrito_producto_producto ON carrito_producto (producto_id)');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT FK_62C02DC2DE2CF6E7 FOREIGN KEY (carrito_id) REFERENCES carrito (id)');
        $this->addSql('ALTER TABLE carrito_producto ADD CONSTRAINT FK_62C02DC27645698E FOREIGN KEY (producto_id) REFERENCES productos (id)');
        $this->addSql('ALTER TABLE categoria CHANGE descripcion descripcion TEXT DEFAULT NULL');
        $this->addSql('DROP INDEX uniq_4e10122d989d9b62 ON categoria');
        $this->addSql('CREATE UNIQUE INDEX slug ON categoria (slug)');
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY FK_4B91E702DB38439E');
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY FK_4B91E702DB38439E');
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY FK_4B91E7027645698E');
        $this->addSql('ALTER TABLE comentario CHANGE texto texto TEXT NOT NULL');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT fk_comentario_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX idx_4b91e702db38439e ON comentario');
        $this->addSql('CREATE INDEX fk_comentario_usuario ON comentario (usuario_id)');
        $this->addSql('DROP INDEX idx_4b91e7027645698e ON comentario');
        $this->addSql('CREATE INDEX fk_comentario_producto ON comentario (producto_id)');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT FK_4B91E702DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id)');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT FK_4B91E7027645698E FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE direccion DROP FOREIGN KEY FK_F384BE95DB38439E');
        $this->addSql('ALTER TABLE direccion DROP FOREIGN KEY FK_F384BE95DB38439E');
        $this->addSql('ALTER TABLE direccion CHANGE usuario_id usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE direccion ADD CONSTRAINT fk_direccion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX idx_f384be95db38439e ON direccion');
        $this->addSql('CREATE INDEX fk_direccion_usuario ON direccion (usuario_id)');
        $this->addSql('ALTER TABLE direccion ADD CONSTRAINT FK_F384BE95DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id)');
        $this->addSql('ALTER TABLE lineas_pedido DROP FOREIGN KEY FK_D2DE2C134854653A');
        $this->addSql('ALTER TABLE lineas_pedido DROP FOREIGN KEY FK_D2DE2C137645698E');
        $this->addSql('DROP INDEX idx_d2de2c137645698e ON lineas_pedido');
        $this->addSql('CREATE INDEX fk_linea_pedido_producto ON lineas_pedido (producto_id)');
        $this->addSql('DROP INDEX idx_d2de2c134854653a ON lineas_pedido');
        $this->addSql('CREATE INDEX fk_linea_pedido_pedido ON lineas_pedido (pedido_id)');
        $this->addSql('ALTER TABLE lineas_pedido ADD CONSTRAINT FK_D2DE2C134854653A FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lineas_pedido ADD CONSTRAINT FK_D2DE2C137645698E FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAADB38439E');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAAD0A7BD7');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAA34676066');
        $this->addSql('DROP INDEX idx_6716ccaa34676066 ON pedidos');
        $this->addSql('CREATE INDEX fk_pedidos_metodo_pago ON pedidos (metodo_pago_id)');
        $this->addSql('DROP INDEX idx_6716ccaadb38439e ON pedidos');
        $this->addSql('CREATE INDEX fk_pedidos_usuario ON pedidos (usuario_id)');
        $this->addSql('DROP INDEX idx_6716ccaad0a7bd7 ON pedidos');
        $this->addSql('CREATE INDEX fk_pedidos_direccion ON pedidos (direccion_id)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAADB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAAD0A7BD7 FOREIGN KEY (direccion_id) REFERENCES direccion (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAA34676066 FOREIGN KEY (metodo_pago_id) REFERENCES metodo_pago (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY FK_DD333C24854653A');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY FK_DD333C27645698E');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY FK_DD333C24854653A');
        $this->addSql('ALTER TABLE pedido_producto DROP FOREIGN KEY FK_DD333C27645698E');
        $this->addSql('ALTER TABLE pedido_producto CHANGE pedido_id pedido_id INT NOT NULL, CHANGE producto_id producto_id INT NOT NULL');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT fk_pedido_producto_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT fk_pedido_producto_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX idx_dd333c27645698e ON pedido_producto');
        $this->addSql('CREATE INDEX fk_pedido_producto_producto ON pedido_producto (producto_id)');
        $this->addSql('DROP INDEX idx_dd333c24854653a ON pedido_producto');
        $this->addSql('CREATE INDEX fk_pedido_producto_pedido ON pedido_producto (pedido_id)');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT FK_DD333C24854653A FOREIGN KEY (pedido_id) REFERENCES pedidos (id)');
        $this->addSql('ALTER TABLE pedido_producto ADD CONSTRAINT FK_DD333C27645698E FOREIGN KEY (producto_id) REFERENCES productos (id)');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY FK_767490E63397707A');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY FK_767490E63397707A');
        $this->addSql('ALTER TABLE productos CHANGE descripcion descripcion TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT fk_productos_categoria FOREIGN KEY (categoria_id) REFERENCES categoria (id) ON DELETE SET NULL');
        $this->addSql('DROP INDEX idx_767490e63397707a ON productos');
        $this->addSql('CREATE INDEX fk_productos_categoria ON productos (categoria_id)');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT FK_767490E63397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('DROP INDEX uniq_ef687f2c1e70a7f ON usuarios');
        $this->addSql('CREATE UNIQUE INDEX telefono ON usuarios (telefono)');
        $this->addSql('DROP INDEX uniq_ef687f2e7927c74 ON usuarios');
        $this->addSql('CREATE UNIQUE INDEX email ON usuarios (email)');
    }
}

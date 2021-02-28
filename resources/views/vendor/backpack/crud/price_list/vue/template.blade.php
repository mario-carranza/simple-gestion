<div id="custom-vue-app">
    <v-app id="app">
        <v-main>
            <v-card class="mt-4 mx-6">
                <v-row class="mx-2">
                    <v-col>
                        <v-text-field 
                            label="Nombre"
                            v-model="priceList.name"
                        >
                        </v-text-field>
                    </v-col>
                </v-row>
                <v-row class="mx-2">
                    <v-col>
                        <v-text-field 
                            label="Codigo"
                            v-model="priceList.code"
                        >
                        </v-text-field>
                    </v-col>
                </v-row>
                <v-card-title>
                    Productos
                    <v-spacer></v-spacer>
                    <v-text-field v-model="search" append-icon="mdi-magnify" label="Buscar" single-line hide-details>
                    </v-text-field>
                </v-card-title>
                <v-row class="mx-2">
                    <v-col>
                        <v-btn color="info">Guardar</v-btn>
                    </v-col>
                </v-row>
                <v-data-table attach auto :headers="headers" :items="products" :search="search">
                    <template v-slot:item.cost="{ item }">
                        @{{ item.cost | formatNumberFilter }}
                    </template>
                    <template v-slot:item.price="{ item }">
                        @{{ item.price | formatNumberFilter }}
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn color="primary" @click="openEditModal(item)">Editar</v-btn>
                    </template>
                </v-data-table>
            </v-card>

            {{-- Modal editar precio y cost --}}
            <v-dialog v-model="dialog" width="500">
                <v-card>
                    <v-card-title class="headline lighten-2">
                        Editar producto
                    </v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    label="Precio"
                                    hide-details="auto"
                                    v-model="selectedProduct.price"
                                    ref="modalFieldPrice"
                                    @focus="$event.target.select()"
                                    @keyup.enter="updateProduct"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    label="Costo"
                                    hide-details="auto"
                                    v-model="selectedProduct.cost"
                                    @keyup.enter="updateProduct"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        
                        <v-btn type="submit" color="primary" text @click="updateProduct">
                            Aceptar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-main>
    </v-app>
</div>

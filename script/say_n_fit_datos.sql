USE SAY_N_FIT;
set names 'utf8mb4';


INSERT INTO EJERCICIO (nombre, descripcion, nivel, musculos) VALUES

('Flexiones de rodillas', 'Empieza a desarrollar tu fuerza realizando esta variante de flexiones.', 'principiante', 'Pectorales, Tríceps, Hombros'),
('Sentadillas', 'Sentadillas con el peso corporal, ideales para practicar tu tecnica y equilibrio. Si la consideras muy facil puedes saltar lo mas alto que puedas, cayendo con la punta de tus pies.', 'principiante', 'Piernas'),
('Plancha', 'Ejercicio que te enseñara a concentrarte para no temblar. Resiste todo lo que puedas con este ejercicio completo.', 'principiante', 'Core'),
('Elevaciones de rodillas colgado', 'Resiste en la barra y levanta tus rodillas hasta donde puedas. Cuando estes listo, podras levantar los pies hasta la barra.', 'principiante', 'Abdominales, Flexores de cadera'),
('Superman', 'Ejercicio para fortalecer la zona lumbar. Si se hace facil, siempre puedes poner un poco de peso para hacerlo mas dificl.', 'principiante', 'Espalda baja, Glúteos'),


('Flexiones', 'Ejercicio clasico, indispensable para volverse mas fuerte.', 'intermedio', 'Pectorales, Tríceps, Hombros'),
('Dominadas', 'Fortalece tu espalda con este ejercicio. Con el tiempo seras capaz de doblar la barra con la fuerza de tus musculos.', 'intermedio', 'Espalda, Bíceps'),
('Fondos en paralelas', 'Trabaja tus brazos en las paralelas.', 'intermedio', 'Pectorales, Tríceps, Hombros'),
('L-sit', 'Ejercicio estatico que pondra a prueba tu fuerza. Domina este ejercicio y obtendras una gran estabilidad', 'intermedio', 'Core'),
('Sentadillas bulgaras', 'LLeva tu equilibrio al siguiente nivel. Con este ejercicio aprenderas a balancear tu peso.', 'intermedio', 'Glúteos, Espalda baja'),


('Flexiones con palmada', 'Empuja el piso para elevarte en el aire. Aumenta tu fuerza y velocidad con este ejercicio.', 'avanzado', 'Pectorales, Tríceps, Hombros'),
('Dominadas al pecho', 'Ve tan alto en la barra como puedas. ', 'avanzado', 'Bíceps, Espalda'),
('Pistols squad', 'Sentadillas a una pierna, demostración maxima de equilibrio.', 'avanzado', 'Piernas, Core'),
('Dragon flag', 'Ejercicio avanzado de abdomen, lleva al limite tu estabilidad y tu core.', 'avanzado', 'Abdominales, Core, Espalda baja'),
('Flexiones a pino asistidas', 'Fortalece tus hombros llevando todo tu peso hasta el piso y levantandolo.', 'avanzado', 'Hombros, Tríceps, Core');

INSERT INTO USUARIO VALUES("admin", "admin", 50, "2025-05-30", "$2y$12$NwREXplclYrskYwpGKyD2.LLDOLAJYNgfwVfm.sJXGBIdcYBpBX2i");
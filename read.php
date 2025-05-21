<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
       <table border="1">
                    <thead>
                        <tr>
                            <th>Jours</th>
                            <th>Cours</th>
                            <th>Heure</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($schedule_data)) {
                             foreach ($schedule_data as $row){
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['jours']) ?></td>
                                    <td><?= htmlspecialchars($row['cours']) ?></td>
                                    <td><?= htmlspecialchars($row['heure']) ?></td>
                                </tr>
                                
                                
                             <?php
                             }
                        }
                            
    ?>
</body>
</html>
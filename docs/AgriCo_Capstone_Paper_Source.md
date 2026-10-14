AgriCo: A Web-Based Soil Color Determination and Scan-Based Soil
Reporting System with Location Mapping and Offline Mobile Application
Support.

A Capstone Project Proposal

Presented to the Faculty of the

College of Information and Computing Sciences

Cagayan State University

Lal-lo Campus

In Partial Fulfillment for the Academic Requirement for the degree
Bachelor of Science in Information Technology

By:

CARL ASHLEY P. CAOILAN\
KENT JORDAN M. ALBERTO\
EMMANUEL JR. A. DE LA CRUZ

APRIL 15

## Chapter I

## The Problem and its Background

Soil color serves as one of the most diagnostic properties for assessing
soil health, providing critical insights into mineral composition,
organic matter content, drainage conditions, and overall land
suitability. For over a century, the Munsell Soil Color Chart has
remained the international standard for soil color determination,
utilized extensively by soil scientists, archaeologists, and
agricultural practitioners worldwide. The traditional methodology
involves manual visual comparison of soil samples against printed color
references under standardized lighting conditions.

However, this approach has limitations that weaken its reliability in
modern agricultural tasks. The process relies on visual judgment, making
it subjective and affected by different lighting and moisture levels.
This leads to potential errors and inconsistent data, particularly when
different observers are involved in large-scale assessments. The
practical challenges extend beyond technical limitations to
accessibility concerns. Manual observation using printed charts can be
time consuming and may produce inaccurate results due to differences in
ambient lighting, subjective human judgment, and limited physical access
to the Munsell chart itself. This process relies heavily on expert
interpretation, making it less accessible to users such as students,
researchers, land developers, and agricultural practitioners who may
lack specialized training in soil science. Consequently, manual methods
often lead to human error, inconsistent results, and difficulty in
documentation, especially when multiple soil samples need to be analyzed
and recorded systematically.

Recent investigations have systematically documented these limitations
through comparative studies between traditional Munsell chart
assessments and digital alternatives. Research conducted by "Fan et
al. 2017" demonstrated that smartphone-based soil color measurements
under natural illumination conditions achieved accuracy comparable to
Munsell chart determinations, while simultaneously reducing subjectivity
and observer uncertainty.

Despite these technological advances, the practical application of
digital soil assessment tools remains limited in developing agricultural
economies such as the Philippines. Current soil assessment practices in
the country predominantly rely on laboratory testing or manual field
observation using traditional Munsell charts, approaches that demand
considerable time, financial resources, and technical expertise. The
Department of Agriculture has consistently emphasized that improving
access to modern agricultural technologies and digital tools is
essential for enhancing productivity and sustainable resource management
across the archipelago. Similarly, the Philippine Statistics Authority
has documented how variations in soil quality and improper land
management practices significantly constrain agricultural output and
optimal land utilization, underscoring the urgent need for more
accessible and efficient soil evaluation methodologies.

The convergence of these challenges---traditional methodological
limitations, documented potential of digital alternatives, and specific
contextual barriers in the Philippines---reveals a critical gap in
available technological solutions. Existing digital soil assessment
applications typically function as isolated tools without integrated
capabilities for automated reporting, geolocation mapping, and offline
functionality. Users must consequently rely on manual data recording or
disparate applications, increasing the risk of data fragmentation, loss,
and operational inefficiency. Without proper documentation
infrastructure and centralized storage mechanisms, tracking soil data
temporally or sharing information among stakeholders becomes
prohibitively difficult, particularly in remote agricultural communities
where internet connectivity remains inconsistent.

In the specific local context of Cagayan State University - Lal-lo
Campus, particularly within the College of Agriculture, soil
identification practices remain confined to visual inspection without
technological augmentation. Agricultural practitioners, researchers, and
land developers continue to depend exclusively on manual Munsell chart
matching, a method that---despite its standardization---frequently
yields inaccurate classifications and inconsistent reporting due to
subjective judgment variations and fluctuating environmental conditions.
Individuals requiring soil information for land assessment, agricultural
research, or development planning often lack access to tools capable of
delivering reliable, immediate results, causing significant delays in
decision-making processes and reducing overall operational efficiency in
soil-related activities.

Moreover, no existing system currently integrates the essential
functionalities of soil scanning, automated report generation, and
location based information storage within a unified platform accessible
through offline mobile support. This absence of comprehensive digital
documentation and spatial mapping capabilities severely constrains the
ability to monitor soil conditions across diverse geographic locations,
directly impacting agricultural planning, land development initiatives,
and environmental assessment activities within the community.

The identified problems fundamentally stem from continued dependence on
manual soil color determination methodologies, insufficient availability
of accessible digital tools tailored to local conditions, and the
absence of integrated systems supporting offline data collection with
centralized reporting capabilities. To address these multifaceted
challenges, this study proposes the development of AgriCo: A Web-Based
Soil Color Determination and Scan-Based Soil Reporting System with
Location Mapping and Offline Mobile Application Support. The system is
designed to integrate soil image capture, automated color analysis
against standardized references, digital report generation, GPS-based
location mapping, and offline functionality to provide a comprehensive,
efficient, and reliable method for soil color determination and data
management.

The relevance of this investigation extends beyond technical innovation
to encompass practical contributions across multiple sectors. As digital
transformation continues to reshape environmental monitoring, land
assessment, agricultural research, and precision farming globally, the
demand for accurate yet accessible soil analysis tools become
increasingly critical. The proposed AgriCo system directly addresses
this need by providing automated soil color determination capabilities
that reduce subjective error while maintaining the scientific rigor of
traditional methods. Furthermore, the system's architecture supports
both mobile field operations and web-based monitoring, ensuring
flexibility for diverse user requirements and technical environments.

The timeliness of this study is particularly evident given the
accelerating demand for mobile applications capable of functioning
reliably in both online and offline environments, especially for
field-based activities where internet connectivity is frequently limited
or non-existent. By synthesizing mobile scanning technology, web-based
data visualization, automated reporting, and geospatial mapping within a
single integrated platform, AgriCo offers a practical and innovative
solution with potential benefits for agricultural practitioners, soil
researchers, land developers, and environmental assessors. This research
consequently contributes to technological advancement in soil analysis
and reporting while addressing specific contextual challenges within the
Philippine agricultural landscape.

Conceptual Framework

Figure 1. Conceptual Framework

The conceptual framework will illustrate a systematic approach to
developing AgriCo, following an Input-Process-Output (IPO) model that
will serve as the foundation for creating an effective soil color
determination and reporting system for farmers, agricultural
practitioners, researchers, and land developers.

The Input component will be grounded in a comprehensive analysis of
existing soil assessment procedures and the challenges encountered by
farmers, including reliance on manual soil checking and the lack of
digital tools for soil condition identification, which result in
inaccurate analysis and the absence of proper location mapping, while
adhering to ISO 25010: Software Quality Standard.

The Process component will involve the analysis of gathered data and the
identification of system requirements, followed by system design using
the SDLC Agile Methodology encompassing Planning, Requirement Analysis,
Designing/Coding, Deployment, Testing, and Maintenance, alongside core
technical processes including Soil Color Detection and Analysis, Offline
Data Storage and Synchronization, Data Validation and Secure Storage,
and Generation of Soil Reports and Recommendations, culminating in
system evaluation for compliance with ISO 25010.

The Output will yield AgriCo: A Web-Based Soil Color Determination and
Scan-Based Soil Reporting System with Location Mapping and Offline
Mobile Application Support, delivering Soil Condition Reports, Soil
Fertility Recommendations, Location-Based Soil Mapping, Offline and
Online Accessible Reports, Analytics Dashboard, Centralized Soil
Information System, and Soft Copy Soil Reports to enhance productivity
and resource management for agricultural stakeholders in the
Philippines.

## Statement of the Problem

This study aims to address the critical gaps in current soil color
determination and reporting methodologies that hinder accurate,
efficient, and accessible soil assessment practices. Despite the
importance of soil color in identifying soil properties and conditions,
many individuals, including those within the Cagayan State University -
Lal-lo Campus COLLEGE OF AGRICULTURE, continue to rely on manual methods
such as visual comparison using the Munsell Soil Color Chart. This
traditional approach depends heavily on human perception, lighting
conditions, and user experience, which may result in inconsistent
interpretation and inaccurate soil classification. Manual recording of
soil observations also increases the risk of data loss, incomplete
documentation, and difficulty in retrieving previous records for
monitoring and analysis.

Furthermore, limited availability of digital tools that can
automatically generate soil reports and store location-based information
in a centralized system poses significant challenges. Users often need
to rely on separate tools for documentation, mapping, and reporting,
which reduces efficiency and increases the possibility of errors. The
absence of an offline-capable system further limits accessibility,
especially in field environments where internet connectivity is unstable
or unavailable. Without proper integration of soil scanning, reporting,
and mapping, it becomes difficult to manage soil data effectively and
support informed decision-making.

Specific Problems

Specifically, this study seeks to answer the following questions:

What are the problems encountered in the existing soil color
determination and reporting methods used by agricultural practitioners,
and students within the CSU Lal-lo College of Agriculture?

What integrated system can be developed for the CSU Lal-lo College of
Agriculture to provide accurate soil color determination and
offline-capable data synchronization using mobile scanning technology,
centralized secure database architecture, and geographic mapping for
agricultural monitoring within the campus and its surrounding
communities?

What features can be implemented to improve accessibility, usability,
and efficiency in soil color determination and reporting for
non-specialist users?

What is the level of compliance of the developed system with ISO
25010:2011 Software, Usability, Efficiency, Maintainability, and
Portability?

## Scope and limitations

Scope

The study will focus on the design and development of AgriCo, an
integrated technological solution specifically designed to modernize
soil color determination and reporting for the Cagayan State
University - Lal-lo Campus College of Agriculture. The system will
centralize its functionality on mobile scanning technology to identify
soil color, which will subsequently trigger the automated generation of
detailed soil reports. To ensure data integrity and accessibility, all
user and soil-related information will be stored in a centralized
database, complemented by location mapping features that will record the
precise geographical coordinates of each collected soil sample.

Recognizing the connectivity challenges in local field environments, the
system will feature a mobile application capable of offline data
collection, allowing users to scan and store information without an
internet connection and synchronize it once a network becomes available.
On the management side, a web-based platform will provide the College of
Agriculture faculty with the tools to view and manage soil records,
oversee soil reference data, and manage pin soil locations, users, and
the application.

The implementation will be divided into two primary components: a
web-based platform for administrative oversight and database
maintenance, and a mobile application for field users to capture soil
color, input sample data, and access generated reports. While the mobile
application will support offline scanning and storage, advanced features
such as map visualization and data synchronization will be designed to
function when an internet connection is established, ensuring a robust
and flexible tool for the university's agricultural research and field
operations.

limitation

This study does not include laboratory-based soil chemical analysis,
advanced soil nutrient testing, or professional agricultural
consultation services. The system focuses only on soil color
determination based on captured images and predefined soil reference
data. Environmental factors such as lighting conditions and camera
quality may affect scanning accuracy, which are beyond the control of
the system.

The study does not cover crop management systems, irrigation planning,
weather monitoring, or full agricultural decision-support platforms.
Integration with external government agricultural databases and
satellite imaging technologies is also excluded. The system is limited
to the features related to soil scanning, reporting, online location
mapping, and data management only. While the mobile application supports
offline soil scanning and local management, the mapping feature remains
an online-only function.

Furthermore, the system is designed to identify a single primary soil
color per scan. It does not provide a percentage breakdown or ratio if
two or more colors are present in the same image. Additionally, the
system does not provide any plant or crop recommendations for the
scanned soil, as the tool is strictly limited to soil classification and
reporting without providing agricultural advice.

## Significance of the Study

College of Agriculture (Students and Faculty)- The system offers a
simple and fast way to find soil color using a phone. It lets students
and instructors create reports, save data, and track locations easily
during field activities. By using this app, the college reduces the need
for manual paper color charts and makes soil documentation more
accurate.

Agricultural Practitioners and Environmental Workers - The system helps
campus farmers and field staff identify soil conditions and create
reports faster. These reports help with land planning on the campus
farm. Also, the map feature lets users track soil traits across
different areas of the college, which helps them make better choices for
crop management.

Administrators and Organizations - The system gives the college managers
one place to control soil data, track soil records, and save
information. This setup makes organizing campus data and making reports
much faster and more efficient than using manual paperwork.

Educational Institutions - This study works as a tool for students to
learn about agriculture and science. It shows how mobile apps and
websites can work together to solve real problems. By using this
technology, students at CSU Lal-lo learn modern ways to check soil and
manage data.

Community and Local Government Units (LGUs)- The system helps the local
community around the campus get soil information easily. This data helps
with local land planning and farming programs. By using digital records,
leaders can plan more accurately and manage resources better for the
town.

Future Researchers - This study is a guide for new research on soil
analysis and mapping. It gives helpful tips on building apps that work
without the internet. Other students can use this project as a model to
create new technology for farming and the environment.

## Definition of Terms

Administrator - The person in charge of managing the system, entering
soil data, and looking after user accounts.

Administrator Input - The act of typing soil information and
descriptions into the system database.

Agile Methodology - A way of building software by working in small
steps, testing often, and making changes easily.

AgriCo - The name of this project. It is a system that uses a phone to
scan soil color and create reports.

Analytics Dashboard - A screen that shows a quick summary of soil
records and reports using charts or lists.

Automated Reporting - A feature that creates a soil report for you
instantly after you scan the soil.

Centralized Database - A single, secure digital folder where all the
system's information is stored.

Data Synchronization - Sending data saved on a phone to the main website
once you have internet.

Department of Agriculture (DA) - The government group in the Philippines
that handles farming and soil rules.

Geolocation - Finding the exact spot on Earth where a soil sample was
taken using GPS.

ISO 25010:2011 - An international rulebook used to check if software is
high-quality and works well.

Location Mapping - A feature that shows where soil samples were
collected on a digital map.

Mobile Application - An app on your phone used to scan soil, save data,
and see reports.

Munsell Soil Color Chart - A classic book of colors used to identify
soil by hand.

Offline Mode - Being able to use the app and scan soil even when there
is no internet.

Online Mode - Using the internet to update records, see maps, or sync
your data.

Philippine Statistics Authority (PSA) - The government office that keeps
track of national data and farming facts.

Scan-Based Reporting - A system that gives you a report based on a photo
of the soil.

Soil Analysis - Checking the soil's condition by looking at its color
and digital data.

Soil Color Determination - Using a phone camera to find the exact color
of the soil.

Soil Data -- All the info collected, like photos, location, and soil
notes.

Soil Database Information -- The collection of soil colors and
descriptions saved in the system.

Soil Fertility Recommendation -- General advice the system gives based
on the soil info entered by the admin.

Soil Reference Data -- The "answer key" the system uses to match your
scan with the right soil type.

Soil Report -- A digital document that shows the scan results and
details about the soil.

Soil Report Template -- A standard layout for how the soil reports
should look.

Soil Scanning -- Taking a picture of soil with a phone to identify it.

System Testing -- Checking the software to make sure everything works
perfectly.

User -- Anyone who uses the app to scan soil or read reports.

User Interface (UI) -- The buttons, menus, and screens you see and touch
when using the app.

Web-Based System -- The website used by admins to manage data and
monitor the whole system.

## Chapter II

## REVIEW OF RELATED LITERATURE

This part presents the literatures and studies from foreign and local
research as well as related readings, which have significant bearings on
the research. Sources include journal articles, research reviews, news
articles, unpublished materials, articles from new sites and websites.

Foreign Literature

Naeimi et al. (2024) explain that knowing exactly what is in soil is
vital for protecting the environment and planning how to use land. They
point out that soil color is a key clue for understanding soil health.
Because of better cameras on smartphones, measuring this color has
become much faster and easier than it used to be. The study describes
how to turn a photo into data through steps like capturing the image,
changing the color format, and pulling out specific details. This
process helps create models that predict soil traits. However, the
researchers note that changing light and wet soil can still make it hard
to get a perfect reading. They suggest using new tools like augmented
reality and real-time processing to make soil testing even simpler for
everyone to use in the field.

In their 2024 study, Kautsar et al. explain that while soil color is
important for checking how healthy soil is, using a manual color chart
can lead to mistakes because different people see colors differently. To
fix this, the researchers used smartphone cameras and a special
"SpyderCheckr" card to make sure the colors in the photos were perfectly
accurate. The study found that using the Lab color space worked much
better than the standard RGB format for catching small changes in the
soil. Their results show that using calibrated mobile phones is a very
accurate way to test soil in many different places and lighting
conditions, making it a great replacement for old-fashioned visual
tests.

According to Wu et al. (2018), soil organic matter (SOM) serves as a
critical indicator for determining soil quality dynamics, yet
traditional measurement methods are often too costly for the needs of
precision agriculture. To address this, their study explored the
quantitative relationship between SOM and soil color by using a digital
camera as an inexpensive, portable tool for surface data acquisition.
Their findings revealed that the red band of the RGB color model showed
the strongest correlation with SOM (r=0.75), especially when using mixed
soil samples to reduce image noise. Furthermore, by transforming data
into the CIELAB color space, the researchers developed a
single-variation predictive model that achieved a high validation
accuracy (R²=0.85). This confirms that digital imaging, combined with
color intensity analysis, provides a stable and highly effective
alternative for the rapid, on-site assessment of soil organic content.

In their 2025 study, Nodi et al. point out that using the traditional
Munsell color book often leads to errors because it relies on how a
person sees color. To fix this, they used smartphone cameras and deep
learning. Because there are so many different soil colors and not enough
photos to train the computer, the researchers used a "patch-based"
method to create more data. While normal computer models were less than
40% accurate, this new patching technique boosted their accuracy to
about 95%. This shows that mobile phones, combined with smart computer
programs, are a very effective and reliable way to identify soil colors
in real-world farming.

According to Baek et al. (2022), the CIELAB color system provides a more
objective and repeatable method for soil classification compared to
traditional visual assessments, which are often subjective. By testing
two soil types under 12 different simulated light conditions, their
study found that converting digital images into  (lightness), 
(green-red), and  (blue-yellow) coordinates allowed the system to remain
accurate despite irregular field illuminance and color temperatures.
This mathematical approach ensures that soil identification remains
consistent, effectively removing the human error common in manual color
matching under varying natural light.

According to Syafutra et al. (2025), soil color is a vital indicator of
physical and chemical properties, yet traditional identification using
the Munsell Soil Color Chart (MSCC) is often compromised by human
subjectivity and lighting inconsistencies. To address this, they
developed a portable color sensor device integrating a TCS3200 sensor
and a microcontroller to automate the process. Validation tests showed
the device achieves 100% accuracy in determining both paper and soil
colors, maintaining consistent results across varying light intensities.
By displaying data directly on an LCD, this portable tool provides a
reliable and efficient alternative for soil classification and
environmental monitoring in the field.

According to Hossain and Kabir (2023), the integration of smartphone
technology and machine learning offers a non-destructive method for
precise Soil Moisture (SM) assessment. By analyzing 629 soil images
captured under varying sunlight conditions, their study compared the
effectiveness of Multiple Linear Regression (MLR), Support Vector
Regression (SVR), and Convolutional Neural Networks (CNN). The results
revealed that SVR models achieved the highest accuracy, particularly
with images taken in indirect sunlight, reaching an R² value of 0.96.
This research highlights the potential for mobile applications to
provide farmers with an accurate, rapid, and easy-to-use tool for
irrigation scheduling and yield improvement.

According to Ferreira da Silva et al. (2025), integrating mobile
technology with colorimetric paper sensors allows for rapid, on-site
soil pH classification with 97% accuracy. Their study demonstrated that
a smartphone-based machine learning model could categorize soil pH into
low, medium, or high levels in minutes, compared to the days required
for traditional laboratory analysis. This system not only reduces costs
but also provides a 9-fold increase in spatial resolution, revealing
soil variations that standard mapping often misses. By enabling
immediate, data-driven soil correction, this innovation supports
sustainable agriculture and benefits farmers in regions where lab access
is limited.

According to Gao et al(2025), the SoilSound system provides a
non-invasive, accessible method for moisture monitoring by utilizing the
built-in acoustic hardware of standard smartphones. By emitting acoustic
chirps and analyzing the reflections through a convolutional neural
network (CNN), the system can estimate soil moisture levels without the
need for calibration or disturbing the soil surface. Their evaluation
across various outdoor locations demonstrated a mean absolute error
(MAE) of 2.39%, effectively tracking moisture levels ranging from 15.9%
to 34.0%. This advancement highlights the potential for ubiquitous
mobile technology to support urban farmers and agricultural communities
with low-cost, real-time environmental data

According to Ben Abderrahmane et al. (2025), the integration of
Geographic Information Systems (GIS), remote sensing, and machine
learning (ML) is essential for overcoming the cost and labor limitations
of traditional laboratory soil analysis. Their research proposes a
unified and modular pipeline that consolidates multi-source soil data to
support transparent and scalable soil quality assessment. Unlike
previous studies that focus on isolated parameters, this comprehensive
roadmap addresses existing modeling challenges to create more adaptive
systems aligned with sustainable land management. By leveraging these
geospatial technologies, the proposed framework provides a blueprint for
the next generation of soil quality systems, making them more efficient
and accessible for large-scale agricultural planning.

Local Literature

The development of digital platforms has become essential for
modernizing Philippine agriculture. According to the Philippine Rice
Research Institute (2023), the PhilRice Soil Information System (PSIS)
was established as an online "data treasury" to provide researchers and
farmers with open access to vital soil data. This system contains a
comprehensive database of soil series, including their physical and
chemical properties, which are used to determine crop suitability and
nutrient management across different provinces.

The application of spatial technology has become a vital tool in
localized agricultural management. According to Calimodag et al. (2024),
a study in the top ten rice-producing barangays of Lambunao, Iloilo,
utilized Geographic Information System (GIS) mapping to evaluate the
soil health of 73.47 hectares. Their research determined critical
parameters such as pH levels, which ranged from 4.75 to 5.84, and
organic matter content. These findings led to the creation of a GIS map
to visually represent soil fertility and provide fertilizer
recommendations. While this study relied on traditional laboratory
analysis to generate maps, it underscores the significant value of
spatial data in agricultural planning.

Furthermore, According to Alejo and Fiegalan (2025), mapping specific
soil types, such as the Maligaya and Bantog series, is critical for
determining the suitability of crops like rice, corn, and onion. Their
research in Nueva Ecija utilized manual identification handbooks and
laboratory methods to delineate these soil zones. However, these
existing systems largely depend on manual classification or pre-existing
records. This study addresses this limitation by developing a soil color
determination and mapping system. By automating the identification of
soil color and integrating it with real-time GPS mapping, the proposed
system provides a faster, digital tool to generate the site-specific
soil data and suitability maps identified as vital by both national
agencies and local researchers.

According to the Bureau of Soils and Water Management (BSWM, 2025), the
Department of Agriculture has implemented Mobile Soil Laboratory (MSL)
units to modernize field-based testing. These units are specifically
deployed to provide immediate soil analysis services for smallholder
farmers in rural or remote areas. While these mobile labs offer on-site
results, they typically require specialized equipment and technical
staff. This study builds on the goal of accessible field testing by
developing a mobile soil color determination and mapping system,
providing a portable, smartphone-based alternative for real-time soil
characterization and spatial mapping.

According to Subia et al. (2023), the Philippines' agricultural economy
relies heavily on soil quality, yet traditional monitoring of soil pH
and nutrient content can be a time-consuming burden for farmers. To
address this, the researchers developed an automated system using a
Raspberry Pi and a camera module to capture and analyze soil samples via
image processing. Central to their method is the Delta-E algorithm,
which mathematically determines the soil's condition and nutritional
levels by comparing color differences. Their findings suggest that by
shifting from manual monitoring to this automated, single-board computer
approach, farmers can more efficiently manage their time while receiving
accurate, real-time data through a Liquid Crystal Display (LCD.

According to Puno et al. (2017), the integration of digital image
processing and Artificial Neural Networks (ANN) provides a reliable
method for determining soil pH and nutrient levels through automated
color analysis. By capturing images of soil samples treated with Soil
Test Kits (STK), the study developed an objective system to identify
seven key parameters: pH, Nitrogen, Phosphorus, Potassium, Zinc,
Calcium, and Magnesium. The researchers utilized the MATLAB neural
network tool to process captured image data, demonstrating that
computational training significantly enhances the speed and accuracy of
color-based results. Ultimately, this approach successfully replaces
subjective visual matching with a precise camera-based digital
assessment, providing qualitative soil health results effectively.

According to Guidang and Gannapao (2019), soil health is a critical
factor in agriculture because extensive chemical use often leaves the
soil highly acidic, which lowers crop yields. Their study developed an
image classifier to automatically assess the health status of soil in
rice farms. By using this digital tool, the researchers found that the
soil at the ASIST rice farms maintained a pH level of 5.8. They
concluded that this digital method allows soil test results to be
identified more accurately and consistently than manual testing,
ensuring the soil remains suitable for rice growing.

According to Dela Cruz et al. (2025), while agricultural technology is
advancing in the Philippines, many local farmers still lack access to
user-friendly, real-time soil monitoring tools. To address this, the
researchers developed an Android-based application specifically for
farmers in Alaminos City to track soil fertility parameters. Using the
Agile Model for development and evaluating the system based on ISO 25010
standards, the study achieved an "Excellent" acceptability rating with a
weighted mean of 3.77. Their findings demonstrate that mobile-based
monitoring systems can effectively provide necessary soil data to both
farmers and city agriculturists, facilitating better-informed decisions
for crop production.

According to Mondejar and Tongco (2019), digital soil mapping (DSM) is
an essential tool for understanding how soil texture fractions vary
across a landscape based on environmental variables derived from Digital
Elevation Models (DEM) and Landsat 8 remote sensing data. Their study in
Barili, Cebu, analyzed 177 soil samples using various geostatistical
models, including Multiple Linear Regression (MLR) and Universal Kriging
(UK). The researchers found that Universal Kriging, when fitted with an
exponential variogram model, yielded the highest predictive accuracy for
clay, sand, and silt (with values up to 0.89). These findings indicate
that integrating free and open-source software like R, SAGA, and QGIS
with terrain data can provide a reliable decision-support framework for
agricultural land-use planning and crop suitability assessments in the
Philippine context.

Mobile and Spatial Soil Information Systems are becoming increasingly
emphasized in the Philippines as indicated by a wide array of national
soil health and digital agriculture research projects from 2018-2025.
According to Department of Agriculture (2025), numerous reports have
clearly shown an increase in using digital tools in conjunction with
traditional farming practices. Although none of these studies focused on
soil color exclusively, they all show a trend toward transitioning away
from traditional, manual based platforms and toward data-driven,
geographically-specific based platforms for enhancing farm management
and production nationwide.

Synthesis

The reviewed foreign and local literature collectively emphasize the
growing importance of integrating digital technologies into soil color
determination to improve agricultural productivity and sustainability.
Foreign studies primarily highlight advancements in smartphone-based
imaging and machine learning technologies for identifying soil physical
properties. These studies demonstrate that automated
systems---especially those using CIELAB color space and calibrated
imaging---can significantly improve accuracy, consistency, and
efficiency compared to traditional methods like the Munsell Soil Color
Chart. Furthermore, foreign research underscores the role of emerging
technologies in enabling real-time, non-invasive analysis through
advanced image processing.

On the other hand, local studies in the Philippines focus more on the
application and adaptation of these technologies within agricultural
systems. They highlight the use of GIS mapping and mobile applications
to streamline soil color determination and field data management. Local
research also emphasizes accessibility, usability, and the need to
support the agricultural community through practical and user-friendly
digital tools that replace manual, subjective observation.

Overall, both foreign and local studies support the development of a
digital soil color determination and mapping system. They establish that
combining smartphone-based imaging, machine learning, and geospatial
technologies can lead to more efficient and accurate results. These
findings provide a strong foundation for the proposed AgriCo system,
which replaces the manual use of the Munsell Soil Color Chart by
integrating real-time detection with mapping capabilities.

Similarities

Both foreign and local literature emphasize the shift from traditional,
manual soil analysis methods to digital and automated systems using
image processing, sensors, and mobile technologies. They consistently
recognize soil characteristics---such as color---as essential indicators
for agricultural productivity and decision-making. While broader
research often includes pH, moisture, and nutrients, AgriCo focuses
specifically on modernizing soil color determination to provide these
critical insights more efficiently.

Additionally, both bodies of research utilize image processing and
computational models, including artificial neural networks and
regression techniques, to analyze soil data objectively. These studies
highlight the limitations of subjective methods like visual color
matching and promote technological solutions like the machine learning
and image processing integrated into AgriCo to improve accuracy and
consistency. Furthermore, they support the concept of precision
agriculture by providing clear, helpful facts that enhance farming
practices and resource management.

Differences

Despite their similarities, foreign and local studies differ in several
aspects. Foreign research focuses more on advanced technologies such as
deep learning, augmented reality, and complex machine learning models,
while local studies tend to use more practical and accessible tools like
Android applications and GIS mapping. In terms of scope, foreign studies
often address large-scale and global applications, whereas local
research is more focused on specific regions within the Philippines.

Additionally, foreign studies explore a wider range of soil parameters
through complex sensing, while local studies mainly concentrate on the
physical characteristics of soil relevant to local crop production.
AgriCo follows this local trend by focusing specifically on soil color
determination, providing a tool that is more direct and easier to use
than the broad analytical systems found in foreign research.

Moreover, foreign research generally assumes access to advanced
equipment and large datasets, whereas local studies consider resource
limitations and prioritize affordability, portability, and ease of use.
A major difference is that while foreign studies often assume constant
connectivity, local research---and specifically the AgriCo
system---prioritizes offline-capable features to support field
operations in remote areas with unstable internet. Finally, local
literature places greater emphasis on usability and adoption by the
College of Agriculture faculty, while foreign studies focus more on
technical performance and methodological innovation.

## Chapter III

## TECHNICAL BACKGROUND

System Requirement

This chapter presents the technical requirements for the development and
implementation of AgriCo: A Web-Based Soil Color Determination and
Scan-Based Soil Reporting System with Location Mapping and Offline
Mobile Application Support.

The system aims to improve the traditional soil identification process
by introducing a digital platform that integrates mobile soil scanning,
automated soil report generation, location mapping, and centralized data
storage. The system supports both online and offline operations,
allowing users to capture soil data in the field even without internet
connectivity.

The development of the system requires appropriate hardware, software,
and human resources (peopleware) to ensure proper system functionality,
reliability, and performance.

Hardware

Hardware components are necessary for the development, and use of the
AgriCo system. These devices support system development, system testing,
and actual system operation by administrators and users.

Development Devices

Laptop: Are primarily used by the developers during the development
phase of the system. Because of their portability, developers can work
in different environments while installing necessary development tools
such as Laravel, PHP, database systems, and testing applications.
Laptops also allow developers to test the system and demonstrate its
functionality.

Smartphone: Are essential for testing the mobile soil scanning
functionality of the AgriCo system. Since the system includes a mobile
application that captures soil images and stores soil data, smartphones
allow developers to evaluate the performance of the scanning feature,
offline data storage, and location detection through GPS services.

Desktop Computer: Are used for testing the web-based administrative
platform. Administrators use desktop systems to access the web interface
where they can upload soil reference data, manage soil reports, and
monitor system activity.

Server Side:

| Hardware Components \| Size \|

| --- \| --- \|

| PROCESSOR \| Intel Core i5 \|

| CPU \| 2.9-4.3 GHz \|

| RAM \| 16 GB or Higher \|

| SSD (for faster performance) \| 512 GB or Higher \|

| Server IP (optional) \| 1 Static IP Address \|

Client Side:

| Hardware Components \| Specification \|

| --- \| --- \|

| Device Type \| Android Smartphone \|

| Processor \| MediaTek Helio G81 Octa-Core Processor \|

| RAM \| 8 GB or higher \|

| Internal Storage \| 128 GB or higher \|

| Camera \| 50 MP or higher with autofocus \|

| GPU \| Mali-G52 MC2 \|

| GPS \| Built-in GPS with high-accuracy location services \|

| Connectivity \| Wi-Fi, 4G/5G Mobile Data \|

Software

Laravel: The primary PHP-based web framework using MVC architecture. It
handles the core system logic, secure authentication, and database
migrations, ensuring the system is scalable and maintainable.

PHP 8.x: The server-side scripting language used to process requests,
communicate with the database, and power the Laravel framework with
modern performance and error handling.

Livewire: A full-stack framework for Laravel that enables the creation
of dynamic, reactive user interfaces (like real-time data updates) using
PHP. This maintains a modern "single-page app" feel for the web
dashboard without complex JavaScript overhead.

Leaflet.js: A lightweight, open-source JavaScript library used for
interactive mapping across the system.

Dart: A client-optimized programming language used to develop
high-performance mobile applications, ensuring a smooth experience
during soil scanning and location mapping.

SQLite: An open-source, serverless relational database engine. Instead
of a separate server process, it stores all soil reference data,
scanning records, and user logs as a single cross-platform file directly
within the application's file system.

HeidiSQL: A lightweight graphical interface tool used to manage the
SQLite database, run queries, and monitor data structures more
efficiently than a command line.

Visual Studio Code (VS Code): The primary Integrated Development
Environment (IDE) used for writing and debugging code, supporting PHP,
Dart, and Blade through specialized extensions.

Figma: A collaborative design tool used to create UI/UX prototypes,
allowing for the visual mapping of the interfaces before development
begins.

Android: The required mobile platforms for the mobile application,
enabling access to essential hardware features such as the camera for
soil capture and GPS for location mapping.

Web Browsers (Chrome/Edge): Modern browsers used by administrators to
access the web-based management platform and view detailed, interactive
soil reports.

Peopleware

The system also depends on human beings and not technology alone to be
successful. The following are required:

Developer: The Developers act as the technical backbone of the AgriCo
system. They are responsible for the entire process, from initial design
to long-term maintenance. Their main duties include coding, debugging,
and testing to ensure the platform is stable and secure.

Beyond the build, they work continuously to improve the system with
updates and better functionality. They also serve as the primary
experts, helping users and fixing any issues quickly so the platform
always runs efficiently.

Administrators: are responsible for encoding soil reference data into
the system. The stored soil information serves as the basis for matching
scanned soil images and generating soil reports.

Users: interact with the system through the mobile application. They can
scan soil samples, capture location data, and access soil reports
generated by the system. The system allows users to store data offline
and synchronize it once internet connectivity becomes available.

System Architecture

Figure 2. System Architecture

The process begins with the Centralized Database System, which acts as
the brain of the operation by storing all user profiles, soil standards,
and scan records. This hub ensures that the mobile app and the web
platform stay perfectly synchronized.

On the ground, students and users interact with the system through the
Mobile Application. Registered users log in to scan soil, generate
reports, and use GPS mapping, while guest users are limited to basic
scanning. You can scan soil offline, but you must use an online
connection to sync data and view maps.

Behind the scenes, Administrators use a web interface to manage the
entire platform. They update soil reference standards and oversee user
accounts to keep the scientific data accurate. Finally, the system works
as a unified ecosystem where all roles and technologies connect to
ensure the soil research is modern and reliable.

## Chapter IV

## RESEARCH METHODOLOGY

Research Design

The research will apply the descriptive study method and system
development process. The records will used descriptive technique to
understand the practices, problems, needs and gaps in handling emergency
response and handling patient records. The Agile model will be used to
develop the proposed system.

Figure 3. Agile Model

The development of AgriCo will follow an Agile approach, which will
break the work into small, repetitive cycles called sprints. This
strategy will allow the team to stay flexible and improve the soil
scanning and mapping tools based on constant feedback.

The process will begin with the Planning phase, where the team will
identify the need for easier soil color testing and will establish a
22-week timeline. During the Requirements phase, the team will list
essential features like real-time analysis and location tagging based on
the specific needs of administrators and users. The Design phase will
then turn these ideas into blueprints, creating a professional look for
the app and planning how the mobile and web systems will share data.

During Development, the system will be built piece-by-piece, focusing
specifically on the scanning technology and the web dashboard. Testing
will happen at every step, during which the team will check scanning
accuracy against real soil samples and will have students and faculty
try the app to find and fix bugs. Once ready, Deployment will involve
launching the website and preparing the mobile app for field use.
Finally, the Maintenance phase will ensure the system stays updated by
fixing minor issues and adding new features to support farmers and
researchers.

Use Case

Figure 4. Use case

The AgriCo system will operate through a mobile application for students
and a web portal for faculty administrators, with both sides working
together through shared data. In the future, students will use the
mobile app to conduct field research by registering accounts and logging
in to add new soil information directly from the site. These users will
also visualize data through point mapping and request mapping for
specific locations, while guests will still be able to use the core soil
scanning feature. All mobile users will manage their own account
settings to keep their profiles current.

At the same time, faculty members will use the administrative web portal
to oversee the entire system and manage authorized access. They will
review and approve user requests to ensure that only verified students
can contribute data to the platform. The administrators will maintain
the system's integrity by managing user lists, soil records, and
geographical mapping data. Finally, the faculty will oversee the
technical deployment of the application to keep it stable. This entire
process will remain synchronized, ensuring that any information added in
the field will be immediately available for review and management on the
web portal.

Entity Relationship

Figure 5. Entity Relationship

You will enter the system as a user, and your information will be stored
in the User table. From there, you will perform a soil scan, which will
be recorded in the Soil_Scan table and linked to you.

Each scan will connect to the Soil_Information table, where soil details
such as name, description, and status will be stored. The system will
then classify the soil color using the Munsell_Color table, ensuring
accurate and standardized identification.

After scanning, you will record the location of the soil in the
Location_Mapping table, capturing its coordinates. However, the data
will not be finalized immediately, as the Admin will review and approve
the information to ensure its validity.

When you need help, you will access the FAQ table, and your interactions
will be tracked in the FAQ_Interaction table.

At the same time, the system will manage applications through the
Application table, while your downloads will be recorded in the
User_Download table.

Overall, you will move through a system where your actions---scanning,
viewing, and downloading---will be recorded, while the Admin will
oversee, manage, and approve the data to maintain accuracy and
reliability.

Attributes and Entities

| ENTITY \| ATTRIBUTES \| DOMAIN \|

| --- \| --- \| --- \|

| User \| User_ID / Full_Name / Email / Password \| 0-9 / a-z, A-Z /
  a-z, A-Z, 0-9, @, . / a-z, A-Z, 0-9, Symbols \|

| Soil_Scan \| Scan_ID / Scan_Note / Scan_Image / Timestamp / User_ID /
  Soil_ID \| 0-9 / a-z, A-Z, 0-9 / a-z, A-Z, 0-9, /, . / 0-9, -, : / 0-9
  / 0-9 \|

| FAQ_Interaction \| Interaction_ID / Action_Type / Timestamp  / User_ID
  / Admin_ID / FAQ_ID \| 0-9 / a-z, A-Z / 0-9, -, : / 0-9 / a-z, A-Z,
  0-9, - / 0-9 \|

| Location_Mapping \| Map_ID / Latitude / Longitude / Timestamp / Status
  / Scan_ID / Soil_ID \| 0-9 / 0-9, ., - / 0-9, ., - / 0-9, -, : / a-z,
  A-Z / 0-9 / 0-9 \|

| Soil_Information \| Soil_ID / Soil_Name / Description / Status /
  Color_ID / Admin_ID \| 0-9 / a-z, A-Z / a-z, A-Z, 0-9 / a-z, A-Z / 0-9
  / 0-9 \|

| Munsell_Color \| Color_ID / Hue  / Value / Chrome  / Full_Code \| 0-9
  / a-z, A-Z, 0-9 / 0-9 / 0-9 / a-z, A-Z, 0-9 \|

| Admin \| Admin_ID / Full_Name / Password / Email \| a-z, A-Z, 0-9, - /
  a-z, A-Z / a-z, A-Z, 0-9, Symbols / a-z, A-Z, 0-9, @, . \|

| System_Actor \| System_Actor_ID / Soil_ID / User_ID / Map_ID /
  Admin_ID / Plant_ID / FAQ_ID / Role \| a-z, A-Z, 0-9, - / 0-9 / 0-9 /
  0-9 / 0-9 / 0-9 / 0-9 / a-z, A-Z \|

| Application_Actor \| App_Actor_ID / Admin_ID / App_ID / Role \| 0-9 /
  0-9 / 0-9 / a-z, A-Z \|

| Application \| App_ID / App_Name / Version / File_Path / Upload_Date
  \| 0-9 / a-z, A-Z / a-z, A-Z,0-9,Symbols / a-z, A-Z,0-9,Symbols / 0-9,
  -, : \|

| Application \| App_ID / App_Name / Version / File_Path / Upload_Date
  \| 0-9 / a-z, A-Z / a-z, A-Z,0-9,Symbols / a-z, A-Z,0-9,Symbols / 0-9,
  -, : \|

| User_Download \| Download_ID / User_ID / App_ID / Download_Date \| 0-9
  / 0-9 / 0-9 / 0-9, -, : \|

| FAQ \| FAQ_ID / Question / Answer / Created_At \| 0-9 / a-z, A-Z, ? /
  a-z, A-Z, 0-9 / 0-9, -, : \|

Flow Chart

Figure 6. Log in/Register Flow Chart

The process will start at the Welcome Interface, moving to the Front
Page where the user will choose to enter as a guest, register a new
account, or log in. If registering, the user will provide their details
to reach the main interface. If logging in, the user will enter their
email and password; if the details are correct, the system will grant
access, while a forgotten password will lead to a reset process.

Once on the Home Page, the user will choose to add soil, scan soil, view
the soil map, or manage their profile. After finishing these tasks, the
system will ask if the user wants to log out. If they choose to stay,
the app will return them to the main interface to continue their work;
if they choose to leave, the process will end as they exit the
application.

Figure 7. History Interface Process Flow

If you choose to search, the system will open the Search Soil tool and
Display the Results. You will then decide if you want to continue
searching. If you say "Yes" the system will restart the search process.
If you say "No" it will return you to the main interface. If you choose
to add a record, the system will prompt you to Input Soil Details and
then Save the Pending Record. Afterward, the system will ask if you want
to add more. Choosing "Yes" will let you add another entry immediately,
while choosing "No" will take you back to the main interface.

Figure 8. Soil Scanning

The system will open the Display Scan Soil Interface to begin. First, it
will Capture a Soil Image and immediately Display a Soil Report based on
that scan. You will then be asked if you want to Pin the Soil Location.
If you choose "No" the system will simply Save the data and return to
the main interface. If you choose "Yes" the system will Tag the Location
and move forward to the next stage of the process.

Figure 9. Location Mapping

If you choose to search, the system will Display Search results for your
query. You will then decide if you want to continue searching. If you
select "Yes" the system will restart the search; if you select "No" it
will return you to the main menu. If you Select a Pin Soil Location, the
system will View Selected Soil Information. You will then be asked if
you want to select another pin. If you select "Yes" it will let you pick
a new location, if you select "No" it will take you back to the main
menu.

Figure 10. User Profile Management and Account Actions Flow

The process begins when you select the User Profile option from the home
page in the first diagram. This action triggers the system to display
your information and presents several actions for you to choose from.
You then decide whether to edit your details, delete your account, or
log out. If you choose to edit, you input new information and the system
saves the changes upon your confirmation. If you choose to delete the
account, the system asks for a final confirmation before removing the
data. Finally, the system completes your chosen task and either updates
your profile or directs you back to the main flow to end the session.

Figure 11. Guest Flow Chart

If you choose to Scan Soil, the system will open the Display Scan Soil
Interface. It will then Capture a Soil Image and Display a Soil Report.
Finally, the system will ask if you want to Save the soil scan. If you
select "Yes" the system will Save the data; if you select "No" it will
simply return you to the main interface. If you select Guest Profile,
the system will ask if you want to Log in. If you select "Yes" you will
be redirected to the login process. If you select "No" the session will
End.

Figure 12. Administrator Dashboard Control Flow

If you choose to Register, you will Enter Information needed and Submit
Information, which leads to the security check. If you choose to Log in,
you will Enter Credentials. If the details are wrong, you can use Forgot
Password? to Input New Password. Once the credentials and Captcha are
verified, you will enter the Admin Interface. Inside the dashboard, you
will Select Option to manage the system. You can choose to Manage Soil,
Manage User, check User Requests, Upload Application, or view the Admin
Profile. After finishing a task, the system will ask if you want to
Logout?. If you select "Yes" the session will End. If you select "No"
you will return to the dashboard.

Figure 13. Manage Soil Data Flow

T If you choose to Register, you will Enter Information needed and
Submit Information, which leads to the security check. If you choose to
Log in, you will Enter Credentials. If the details are wrong, you can
use Forgot Password? to Input New Password. Once the credentials and
Captcha are verified, you will enter the Admin Interface. Inside the
dashboard, you will Select Option to manage the system. You can choose
to Manage Soil, Manage User, check User Requests, Upload Application, or
view the Admin Profile. After finishing a task, the system will ask if
you want to Logout?. If you select "Yes" the session will End. If you
select "No" you will return to the dashboard.

Figure 14. Manage User Accounts Flow

If you choose to Delete User, the system will ask for a Delete
Confirmation. If you select "Yes" the system will show that the user was
Delete Successfully and return you to the main menu. If you select "No"
you will simply be taken back to the main menu without any changes. If
you choose to Edit User, the system will Display Edit User Pop up
Interface where you will Input New Information. The system will then ask
if you want to Save new information?. If you select "Yes" you will see
Changes saved. If you select "No" the system will show Changes not
saved. In both cases, you will then return to the main menu.

Figure 15. User Request Approval

If you select a request, the system will Display Request Information for
your review. You will then decide whether to Accept User Request?. If
you select "Yes" the system will show that the user is Accepted
Successfully. If you select "No" it will show that the request is
Declined Successfully. After either action, you will return to the main
dashboard. If you choose to Search Request, the system will Display
Search results. You will then be asked if you want to Continue Search?.
If you select "Yes" it will restart the search process, if you select
"No" it will return you to the main dashboard. If no action is taken,
the system will simply refresh the Display User Request Interface.

Figure 16. Application Upload and Publishing

The system will begin by showing the Display Manage App Interface. From
here, you will be asked if you want to Upload application?. If you
select "Yes" the system will show an Uploading... status until the
process is complete and the App Published. After the publication is
finished, you will move to the next section. If you select "No" the
system will simply refresh and stay on the current interface.

Figure 17. Administrator Profile Management

If you choose to Edit Information, you will Input New Information. The
system will then ask if you want to Save new information?. If you select
"Yes" the system will show Changes saved. If you select "No" it will
show Changes not saved. Both options will return you to the main
dashboard. If you choose to Delete Account, the system will ask Delete
your account?. Selecting "Yes" will show Deleted Successfully and take
you to the registration screen, while selecting "No" will return you to
the dashboard. If you choose to Log Out, the system will immediately
take you back to the registration screen. If no action is taken, the
system will simply refresh the current profile view.

.

Figure 18. Website flow

The process begins when you land on the Home Page, which serves as your
main hub for all resources. You then choose between three paths: the
download section, the Help Center, or the soil information section. If
you want the software, you go to the download area and decide whether to
start the file transfer. If you need help, you enter the Help Center to
browse frequently asked questions or search for specific answers. If you
choose the soil information section, the system displays all the
recorded details for you to view. Finally, the system completes your
request by finishing the download or finding your answers and then
directs you back to the home page to continue or exit.

Data Flow

Level 0

Figure 19. Administrator Profile Management and Account Actions Flow

The process will start when the User submits their registration or login
details to enter the system. Once inside, the User will upload soil
images and GPS coordinates to get a digital analysis. They will also
send search queries to find old records or submit profile edits and
questions. In response, the system will send back detailed soil reports,
updated history logs, and map pins. It will also provide app updates to
keep the software running smoothly.

Meanwhile, the Admin will manage the system by logging in to provide new
soil data and send approval actions. The system will forward all user
requests and activity logs to the Admin for review. After the Admin
finishes a task, the system will send back confirmation notices to show
that the updates are successful. This flow will ensure that data moves
quickly and accurately between everyone involved.

Level 1

Figure 20. Administrator Profile Management and Account Actions Flow

In the future, the AgriCo System will break down into several
specialized sub-processes to manage data efficiently.

The journey will begin with the Login/Register and Manage Profile
sections, where the system will take user credentials and save them into
the secure data store. When a user captures a photo, the Scan Soil
process will analyze the image and pass the findings to the Result
process. The system will then display a report to the user and save the
details in the soil information database.

For mapping, the Soil Mapping process will send a request to the Admin
Management hub. Once the Admin approves the location, the data will move
to the soil map store and update the user's view with new pins. If a
user has questions, the FAQ process will search for answers in the
database and send them back immediately. Finally, the History process
will retrieve all past records so the user can review their previous
work. Throughout this flow, the Admin will oversee everything, approving
data and managing the system to keep all information accurate.

Hierarchical Input Process Output

Figure 21. User HIPO

When a user registers, they will enter their details, validate the data,
and complete the process. After logging in, they will access the User
Dashboard to manage various tasks. From there, they will be able to add
soil records or creating new entries, perform soil scans by capturing
images and tagging locations, and use the soil map to pin locations and
view specific details. Users will also manage their profile to edit
information, delete their account, or log out.

Guests will use a separate dashboard with fewer options. They will be
able to perform soil scans by capturing soil data and use a guest
profile to either log in to a full account or log out of the session.

Figure 22. Admin HIPO

When a user registers, they enter their details, check the information,
and complete the process. After logging in, they can use the dashboard.
From there, they can view their history, search and filter data, scan
soil by capturing and tagging it and use a soil map to see locations and
details. Users can also manage their profile by editing their
information, deleting their account, or logging out. Guests can also use
some features like viewing history and scanning soil, but they have
fewer options than registered user.

Figure 23. Website HIPO

The website part is for everyone. It allows people to download the
application, visit the help center, search FAQS, and view general soil
information without logging in.

Locale of the Study

The study will be conducted at Cagayan State University (CSU) Lal-lo
Campus, located at Sta. Maria, Lal-lo, Cagayan. While the campus
consists of various departments and offices, the research will focus
specifically on the College of Agriculture.

The implementation will center on the activities of students and faculty
researchers within the department who handle agricultural data and field
reporting. It will specifically address the perspective of these users
regarding the transition from manual soil color assessment to a digital,
scan-based reporting system. The study will be executed within the
campus's agricultural laboratories and experimental field sites,
providing a direct environment to test the system's location mapping and
offline mobile support under real-world connectivity constraints.

Participants of the Study

The participants of the study as presented in Table 3 composed of the
following:

| Participants \| Number \| Percentage \|

| --- \| --- \| --- \|

| IT expert \| 3 \| 25% \|

| Agriculture Faculty \| 3 \| 25% \|

| Agriculture Students \| 6 \| 50% \|

| Total \| 12 \| 100% \|

Table 3. Participants of the Study

The IT Experts act as the technical reviewers for the study. They check
the system to make sure it works well and meets high digital standards.
They specifically test the platform using the ISO 25010 rules to ensure
the software is high quality. Their job is to verify that the mobile app
and web dashboard are safe and reliable. They also make sure the data
stays correct when the system syncs information from offline to online.

The Agriculture Faculty Staff act as the main experts and reviewers for
the system's scientific results. They check how accurate the soil
reports and location maps are. By testing these features from a
professional view, they make sure the system is useful for farm records.
They also ensure the data meets the school's requirements for managing
soil information.

The Agriculture Students act as the main users and field testers for the
system. They take the mobile app to the CSU Lal-lo experimental sites to
see how well it works in real field conditions. They test if the app is
easy to use and helpful for their daily tasks. Their feedback shows if
the platform truly makes it easier to identify soil color and collect
data for future researchers.

Data Gathering Procedure

Prior to data gathering and collection, the researchers performed the
following steps:

seek formal approval from the CSU Lal-lo College of Agriculture
administration for the official conduct of the study within the
institution's premises.

A formal request letter will be sent to the College of Agriculture to
schedule the necessary data gathering sessions and field demonstrations
according to the established study objectives.

communicate with the Agriculture Faculty and Students to inform them of
the evaluation process for the soil color determination and reporting
system, ensuring their expert feedback will be documented for system
enhancement.

Comprehensive guidance and orientation will be provided to the
Agriculture Faculty and Students on the operation of the web-based
dashboard and the offline-capable mobile application. Following the
orientation and field inspection, the participants will assess the
usability and effectiveness of the system.

retrieve, review, and seek final approval for the completed evaluation
forms used during the assessment of the AgriCo platform to ensure the
data is accurate and ready for analysis.

Ethical Considerations on the Data

The following are the research instruments used in gathering the needed
data for the study:

Permission and Authorization -- Formal approval from the college leaders
and stakeholders before collecting any data. This step ensures the study
follows all school rules. It also makes sure everyone involved
understands the goals of the research before they start.

Confidentiality and Data Privacy -- Keeping all information from faculty
and students private and protected. The system uses security measures,
like an offline SQLite database for local storage and secure server
rules for the web dashboard. These steps prevent unauthorized people
from seeing the data. Finally, this ensures the study follows all data
privacy standards to keep information safe.

Avoiding Harm -- The process begins by ensuring that no people or school
records are negatively affected during the software testing. The data
collection methods, such as soil scanning and field mapping, are
designed to be non-intrusive. This ensures that the activities fit into
existing school practices without causing any disruption. Finally, the
research focuses on providing a safe and helpful tool for everyone
involved.

Integrity and Accuracy -- Collecting and analyzing all field data and
system results with complete honesty. The researchers ensure that the
soil color results and performance measurements are reported accurately.
This step prevents any wrong information or bias in the study's
findings. Finally, the system maintains high standards to ensure all
data stays true and reliable for the final report.

WIREFRAME

Figure 5. User Interface

The app starts with a Welcome Screen and a few introduction pages that
explain how everything works. From there, you can register by entering
your details and verifying your account. Once you log in, you arrive at
the User Dashboard, which is the main hub for all your activities.Within
this dashboard, you can manage soil records by searching for existing
data or creating new entries. If you need to analyze a sample, you can
use the camera to take a picture, let the system process the scan, and
then view a detailed soil report. You can also use the soil map to pin
specific locations and see information about the ground in those areas.
The home screen makes it easy to find what you need with a search bar
and a history section for your past results. Finally, you have full
control over your profile, where you can update your information, delete
your account, or log out when you are finished.

Figure 5. Admin Interface

The AgriCo system is a professional control center designed for clear
and secure management. It begins with a Landing Page and intro screens
that lead to a secure login. To protect the system, the login includes a
safety check to block bots and unauthorized access.

Once inside, the Main Dashboard serves as the primary hub. It provides a
visual overview of all activity, showing trends in soil colors and a
list of the most searched soil types. This helps leaders quickly see
which areas or soil varieties are being studied the most.

For organizing information, the Soil Database stores a complete list of
every sample collected. Alongside this, the User Management section
shows who is currently active and tracks how many scans each team member
has completed. There is also an Account Requests area where managers can
review and approve new users or share specific data. Finally, the System
Updates section allows for the easy upload of the latest app versions,
ensuring everyone in the field is using the most up-to-date tools.

Figure 5. Admin Interface

The process starts on the Home Page, which works as the main menu. From
here, you can choose from three main sections to find what you need.

If you want the app, you will go to the Download Section. If you have
questions, you will visit the FAQ Page to find quick answers. If you
want to study soil details, you will go to the Soil Information Page to
see the records.

Finally, the website will give you the file you need or show you the
data you requested. When you are finished, you can return to the Home
Page or close the site to exit.

APPENDICES

BIBLIOGRAPHY (APA 7TH EDITION)

Arslan, S., & Gelen, G. (2024). Deep learning-based soil color
determination using a smartphone camera. Computers and Electronics in
Agriculture, 220, 108937. https://doi.org/10.1016/j.compag.2024.108937

Bartholomeus, H. M., Schaepman, M. E., Kooistra, L., Stevens, A.,
Hoogmoed, W. B., & Spaargaren, O. S. (2008). Rapid determination of soil
organic matter content based on soil colour obtained by a digital
camera. ResearchGate. https://www.researchgate.net/publication/324254846

Chen, S., et al. (2025). Application of machine learning in soil
property prediction. Sensors, 25(1), 287.
https://doi.org/10.3390/s25010287

Ferreira da Silva, A., et al. (2023). Development of portable color
detector for Munsell Soil Color determination. PLOS ONE, 18(10),
e0317739. https://doi.org/10.1371/journal.pone.0317739

Ibrahim, M., et al. (2023). A roadmap of geospatial soil quality
analysis systems. ResearchGate.
https://www.researchgate.net/publication/398559299

Li, H., et al. (2023). SoilSound: Smartphone-based soil moisture
estimation. ResearchGate.
https://www.researchgate.net/publication/395474476

Santos, J., et al. (2023). Digital soil mapping and its application in
agriculture. Agriculture, 13(3), 574.
https://doi.org/10.3390/agriculture13030574

Wang, Y., et al. (2023). Soil color determination using digital imaging
and machine learning. Computers and Electronics in Agriculture, 212,
106877. https://doi.org/10.1016/j.compag.2023.106877

Bureau of Soils and Water Management. (n.d.). National soil health
program: Inaugural operation of the mobile soil laboratory. Department
of Agriculture.
https://www.bswm.da.gov.ph/national-soil-health-program-inaugural-operation-of-the-mobile-soil-laboratory/

Dela Cruz, R., et al. (2023). Digging deeper: Soil quality monitoring
system for Alaminos City farmers. International Journal of Research and
Innovation in Social Science.
https://rsisinternational.org/journals/ijriss/articles/digging-deeper-soil-quality-monitoring-system-for-alaminos-city-farmers/

Hernandez, M., et al. (2019). Enhanced soil classification technique in
Cagayan Valley, Philippines using Artificial Neural Network (ANN).
ResearchGate. https://www.researchgate.net/publication/336365684

Pascual, J., et al. (2022). Development of a web-based soil information
system. E-Journals PH. https://ejournals.ph/article.php?id=18945

Philippine Rice Research Institute. (n.d.). Philippine soils online data
treasury.
https://www.philrice.gov.ph/philippine-soils-online-data-treasury/

Tolentino, L., et al. (2023). Automated soil nutrient analysis system
with LCD display. E-Journals PH.
https://ejournals.ph/article.php?id=19732

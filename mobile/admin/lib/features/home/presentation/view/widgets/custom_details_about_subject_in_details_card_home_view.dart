import 'package:flutter/material.dart';
import '/core/paddings/padding_with_child/symmetric_padding_with_child.dart';
import '/core/sized_boxs/heights.dart';
import '/features/home/presentation/view/widgets/custom_name_and_image_for_teacher_home_view.dart';
import '/features/home/presentation/view/widgets/custom_name_subject_text_home_view.dart';
import '/features/home/presentation/view/widgets/custom_text_and_image_in_details_card_home_view.dart';
import '/gen/assets.gen.dart';

class CustomDetailsAboutSubjectInDetailsCardHomeViewSection
    extends StatelessWidget {
  const CustomDetailsAboutSubjectInDetailsCardHomeViewSection({
    super.key,
    required this.subjectName,
    required this.course,
    required this.classRoom,
    required this.supervioserName,
    required this.imageUrl,
  });
  final String subjectName, course, classRoom, supervioserName, imageUrl;
  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: SymmetricPaddingWithChild.vertical8(
        context: context,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            CustomNameSubjectTextHomeView(subjectName: subjectName),
            CustomTextAndImageInDetailsCardHomeView(
              text: course,
              pathImage: Assets.images.carbonCategoryImage.path,
            ),
            Heights.height5(context: context),
            CustomTextAndImageInDetailsCardHomeView(
              text: classRoom,
              pathImage: Assets.images.locationImage.path,
            ),
            Heights.height5(context: context),
            CustomNameAndImageForTeacherInSomeDetailsCardHomeView(
              supervioserName: supervioserName,
              imageUrl: imageUrl,
            ),
          ],
        ),
      ),
    );
  }
}

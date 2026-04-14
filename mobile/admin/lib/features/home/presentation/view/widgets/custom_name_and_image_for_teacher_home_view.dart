import 'package:flutter/material.dart';
import '/core/sized_boxs/widths.dart';
import '/features/home/presentation/view/widgets/custom_image_for_teacher_in_details_card_home_view.dart';
import '/features/home/presentation/view/widgets/custom_name_teacher_in_details_card_home_view.dart';

class CustomNameAndImageForTeacherInSomeDetailsCardHomeView
    extends StatelessWidget {
  const CustomNameAndImageForTeacherInSomeDetailsCardHomeView({
    super.key,
    required this.supervioserName,
    required this.imageUrl,
  });
  final String supervioserName, imageUrl;
  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.end,
      children: [
        CustomNameTeacherInDetailsCardHomeView(name: supervioserName),
        Widths.width9(context: context),
        CustomImageForTeacherInDetailsCardHomeView(imageUrl: imageUrl),
      ],
    );
  }
}

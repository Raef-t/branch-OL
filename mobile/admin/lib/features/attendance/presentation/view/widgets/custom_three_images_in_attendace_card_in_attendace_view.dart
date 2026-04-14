import 'package:flutter/cupertino.dart';
import '/core/sized_boxs/heights.dart';
import '/gen/assets.gen.dart';

class CustomThreeImagesInAttendaceCardInAttendaceView extends StatelessWidget {
  const CustomThreeImagesInAttendaceCardInAttendaceView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Assets.images.dateImage.image(),
        Heights.height15(context: context),
        Assets.images.watchImage.image(),
        Heights.height15(context: context),
        Assets.images.watchImage.image(),
      ],
    );
  }
}
